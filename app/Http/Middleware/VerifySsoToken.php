<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use App\Models\User;

class VerifySsoToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->session()->get('sso_token') ?? $request->query('access_token') ?? $request->query('token');

        if ($request->has('refresh_token')) {
            $request->session()->put('sso_refresh_token', $request->query('refresh_token'));
        }

        if (!$token) {
            return $this->redirectToSso($request);
        }

        $publicKeyPath = config('services.sso.public_key') ?: (file_exists(base_path('keys/public.pem')) ? base_path('keys/public.pem') : storage_path('app/keys/public.pem'));

        // Fallback create keys dir if missing
        if (!file_exists($publicKeyPath)) {
            $ssoBase = config('services.sso.internal_url') ?: config('services.sso.base_url') ?: env('SSO_BASE_URL', 'http://sso-engine.test');
            try {
                $pkResponse = \Illuminate\Support\Facades\Http::timeout(3)->get(rtrim($ssoBase, '/') . '/public-key');
                if ($pkResponse->successful()) {
                    if (!is_dir(dirname($publicKeyPath))) {
                        mkdir(dirname($publicKeyPath), 0755, true);
                    }
                    file_put_contents($publicKeyPath, $pkResponse->body());
                }
            } catch (\Exception $e) {
                // ignore
            }
        }

        if (!file_exists($publicKeyPath)) {
            abort(500, 'SSO public key not found.');
        }

        try {
            $publicKey = file_get_contents($publicKeyPath);
            $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));

            // INSTANT SLO: Cek Redis Blacklist (< 0.1ms)
            if (!empty($decoded->jti) && $this->isBlacklisted($decoded->jti)) {
                \Illuminate\Support\Facades\Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return $this->redirectToSso($request);
            }

        } catch (ExpiredException $e) {
            $refreshToken = $request->session()->get('sso_refresh_token');
            if ($refreshToken) {
                // Attempt to refresh the token via SSO Engine
                $ssoBase = config('services.sso.internal_url') ?: config('services.sso.base_url') ?: env('SSO_BASE_URL', 'http://sso-engine.test');
                $clientId = config('services.sso.client_id') ?: env('SSO_CLIENT_ID', 'inventory-app');
                $response = \Illuminate\Support\Facades\Http::timeout(5)->post(rtrim($ssoBase, '/') . '/refresh-token', [
                    'client_id' => $clientId,
                    'refresh_token' => $refreshToken
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $token = $data['access_token'];
                    
                    $request->session()->put('sso_token', $token);
                    $request->session()->put('sso_refresh_token', $data['refresh_token']);
                    
                    $publicKey = file_get_contents($publicKeyPath);
                    $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));

                    // Cek blacklist juga pada token baru
                    if (!empty($decoded->jti) && $this->isBlacklisted($decoded->jti)) {
                        \Illuminate\Support\Facades\Auth::logout();
                        $request->session()->invalidate();
                        $request->session()->regenerateToken();
                        return $this->redirectToSso($request);
                    }
                } else {
                    \Illuminate\Support\Facades\Auth::logout();
                    $request->session()->forget('sso_token');
                    $request->session()->forget('sso_refresh_token');
                    return $this->redirectToSso($request);
                }
            } else {
                \Illuminate\Support\Facades\Auth::logout();
                $request->session()->forget('sso_token');
                return $this->redirectToSso($request);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Auth::logout();
            $request->session()->forget('sso_token');
            return $this->redirectToSso($request);
        }

        // Bypass DB check if user already logged in with same sso_user_id
        $currentUser = \Illuminate\Support\Facades\Auth::user();
        if ($currentUser && $currentUser->sso_user_id === $decoded->sub) {
            $request->session()->put('sso_token', $token);
            return $next($request);
        }

        $user = User::where('sso_user_id', $decoded->sub)->orWhere('email', $decoded->email)->first();
        $isSsoAdmin = isset($decoded->is_sso_admin) && $decoded->is_sso_admin;

        if (!$user) {
            if ($isSsoAdmin) {
                $user = User::create([
                    'sso_user_id' => $decoded->sub,
                    'name'        => $decoded->username ?? explode('@', $decoded->email)[0],
                    'email'       => $decoded->email,
                    'role'        => 'admin',
                    'synced_at'   => now(),
                    'password'    => bcrypt(str()->random(16)),
                ]);
            } else {
                $pendingRole = \Illuminate\Support\Facades\DB::table('pending_role_assignments')
                    ->where('email', $decoded->email)
                    ->where('status', 'pending')
                    ->where('expires_at', '>', \Carbon\Carbon::now())
                    ->first();

                if (!$pendingRole) {
                    abort(403, 'Akses Ditolak: Anda belum memiliki akses (Role) di aplikasi Inventory. Silakan hubungi Administrator IT untuk melakukan Provisioning.');
                }

                $user = User::create([
                    'sso_user_id' => $decoded->sub,
                    'name'        => $decoded->username ?? explode('@', $decoded->email)[0],
                    'email'       => $decoded->email,
                    'role'        => $pendingRole->role,
                    'synced_at'   => now(),
                    'password'    => bcrypt(str()->random(16)), 
                ]);

                \Illuminate\Support\Facades\DB::table('pending_role_assignments')
                    ->where('id', $pendingRole->id)
                    ->update(['status' => 'used']);
            }
        } else {
            $updateData = [
                'sso_user_id' => $decoded->sub,
                'synced_at'   => now(),
            ];
            
            if ($isSsoAdmin && $user->role !== 'admin') {
                $updateData['role'] = 'admin';
            }
            
            $user->update($updateData);
        }

        \Illuminate\Support\Facades\Auth::login($user);
        $request->session()->put('sso_token', $token);

        return $next($request);
    }

    private function isBlacklisted(string $jti): bool
    {
        $host = env('REDIS_HOST', '127.0.0.1');
        $port = (int) env('REDIS_PORT', 6379);

        $fp = @fsockopen($host, $port, $errno, $errstr, 0.2);
        if (!$fp) {
            return false;
        }

        $key = 'sso_blacklist:jti:' . $jti;
        $cmd = "*2\r\n$6\r\nEXISTS\r\n$" . strlen($key) . "\r\n" . $key . "\r\n";
        fwrite($fp, $cmd);
        $res = fgets($fp);
        fclose($fp);

        return trim((string) $res) === ':1';
    }

    protected function redirectToSso(Request $request)
    {
        $state = bin2hex(random_bytes(16));
        $request->session()->put('sso_state', $state);

        $clientId    = config('services.sso.client_id') ?: env('SSO_CLIENT_ID', 'inventory-app');
        $redirectUri = config('services.sso.redirect_uri') ?: env('SSO_REDIRECT_URI', 'http://inventory.test/callback');
        $baseUrl = config('services.sso.base_url') ?: env('SSO_BASE_URL', 'http://sso.localhost');

        if ($request->isSecure() || str_contains($request->getHost(), 'sslip.io')) {
            $baseUrl = str_replace('http://sso.', 'https://sso.', $baseUrl);
            $redirectUri = str_replace('http://inventory.', 'https://inventory.', $redirectUri);
        }

        $query = http_build_query([
            'client_id'    => $clientId,
            'redirect_uri' => $redirectUri,
            'state'        => $state,
        ]);

        return redirect()->away(rtrim($baseUrl, '/') . '/authorize?' . $query);
    }
}

