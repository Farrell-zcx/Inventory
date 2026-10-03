<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class KelolaPenggunaController extends Controller
{
    public function index()
    {
        // Sinkronisasi realtime dari SSO Engine HANYA untuk user yang sudah ada di lokal
        try {
            $ssoUsers = DB::connection('sso_engine')->table('users')->get()->keyBy('id');
            $localUsers = User::whereNotNull('sso_user_id')->get();

            foreach ($localUsers as $localUser) {
                if (!$ssoUsers->has($localUser->sso_user_id)) {
                    // Jika user sudah dihapus di SSO pusat, hapus juga di lokal
                    $localUser->delete();
                } else {
                    // Jika masih ada, pastikan email dan nama up-to-date dengan pusat
                    $ssoUser = $ssoUsers->get($localUser->sso_user_id);
                    if ($localUser->email !== $ssoUser->email || $localUser->name !== $ssoUser->username) {
                        $localUser->update([
                            'email' => $ssoUser->email,
                            'name' => $ssoUser->username,
                            'synced_at' => now(),
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('Gagal sinkronisasi dengan SSO Engine: ' . $e->getMessage());
        }

        $users = User::orderBy('name')->get();
        $pendingUsers = DB::table('pending_role_assignments')
                            ->where('status', 'pending')
                            ->orderBy('created_at', 'desc')
                            ->get();
                            
        return view('admin.pengguna.index', compact('users', 'pendingUsers'));
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,staff'
        ]);

        $user->update(['role' => $request->role]);

        return redirect()->route('admin.pengguna.index')->with('success', 'Peran pengguna berhasil diperbarui.');
    }

    public function storeProvisioning(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'role'  => 'required|in:admin,staff'
        ]);

        if (User::where('email', $request->email)->exists()) {
            return redirect()->back()->with('error', 'Pengguna dengan email tersebut sudah terdaftar di sistem. Silakan ubah rolenya di tabel bawah.');
        }

        DB::table('pending_role_assignments')->updateOrInsert(
            ['email' => $request->email],
            [
                'role'       => $request->role,
                'status'     => 'pending',
                'assigned_by'=> auth()->id(),
                'expires_at' => now()->addDays(7),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        return redirect()->route('admin.pengguna.index')->with('success', 'Pre-Provisioning berhasil! Pengguna kini dapat login ke Inventory dengan role ' . ucfirst($request->role) . '.');
    }
}

