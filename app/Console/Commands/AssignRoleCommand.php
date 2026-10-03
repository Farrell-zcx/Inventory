<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AssignRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sso:assign-role';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign role to an email for JIT provisioning';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Inventory SSO Role Assignment ===');
        
        $email = $this->ask('Email karyawan (harus sama persis dengan akun SSO)');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Format email tidak valid.');
            return;
        }

        $roles = [
            '1' => 'admin',
            '2' => 'approver',
            '3' => 'staff',
        ];

        $roleChoice = $this->choice(
            'Role di Inventory',
            $roles,
            '3'
        );

        $selectedRole = $roles[$roleChoice] ?? $roleChoice;

        // Check for existing pending assignment
        $existing = DB::table('pending_role_assignments')
            ->where('email', $email)
            ->where('status', 'pending')
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($existing) {
            $this->warn("Email {$email} sudah memiliki assignment role ({$existing->role}) yang masih pending.");
            if (!$this->confirm('Apakah Anda ingin menimpanya?')) {
                return;
            }
            DB::table('pending_role_assignments')
                ->where('email', $email)
                ->where('status', 'pending')
                ->delete();
        }

        DB::table('pending_role_assignments')->insert([
            'email' => $email,
            'role' => $selectedRole,
            'assigned_by' => 1, // Assuming admin ID 1 runs this CLI, or we could leave it 0
            'status' => 'pending',
            'expires_at' => Carbon::now()->addDays(7),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $this->info("[OK] Role '{$selectedRole}' berhasil di-assign untuk {$email}");
        $this->line("Berlaku sampai login pertama atau 7 hari, mana yang lebih dulu.");
    }
}
