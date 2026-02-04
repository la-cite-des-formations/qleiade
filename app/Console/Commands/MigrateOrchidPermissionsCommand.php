<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Models\User;
use Spatie\Permission\Models\Permission;

class MigrateOrchidPermissionsCommand extends Command
{
    protected $signature = 'app:migrate-orchid-permissions';
    protected $description = 'Migrate permissions from Orchid JSON column to Spatie tables';

    public function handle()
    {
        $this->info('Starting permission migration...');
        
        $users = User::all();
        $bar = $this->output->createProgressBar(count($users));

        $bar->start();

        foreach ($users as $user) {
            $permissionsData = json_decode($user->getAttributes()['permissions'] ?? '[]', true);

            if (empty($permissionsData)) {
                $bar->advance();
                continue;
            }

            foreach ($permissionsData as $permissionName => $value) {
                // Orchid stores permissions like "platform.systems.users" => 1 or 0
                // Use explicit boolean check
                if ((bool) $value) {
                    // Create permission if it doesn't exist
                    $permission = Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
                    
                    // Unset the attribute so Spatie uses the relationship
                    unset($user->permissions);

                    // Assign permission to user
                    // We use the safe guard_name assignment to avoid ambiguity
                    $user->givePermissionTo($permission);
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Permissions migrated successfully.');
    }
}
