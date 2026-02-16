<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Models\User;

class MigrateOrchidToSpatie extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:migrate-orchid-to-spatie 
                            {--dry-run : Exécuter en mode simulation sans modifications}
                            {--force : Forcer la migration sans confirmation}
                            {--cleanup : Supprimer les tables et colonnes Orchid après migration}
                            {--keep-backup : Conserver les tables Orchid en les renommant avec le suffixe _backup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migre les permissions et rôles d\'Orchid vers Spatie Permission';

    /**
     * Statistiques de migration
     */
    private array $stats = [
        'roles_created' => 0,
        'permissions_created' => 0,
        'users_migrated' => 0,
        'errors' => [],
    ];

    /**
     * Mapping des permissions par rôle (pour association ultérieure)
     */
    private array $rolePermissionsMap = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('🚀 Migration Orchid → Spatie Permission');
        $this->newLine();

        if ($isDryRun) {
            $this->warn('⚠️  MODE SIMULATION ACTIVÉ - Aucune modification ne sera effectuée');
            $this->newLine();
        }

        // Vérification des tables Orchid
        if (!$this->checkOrchidTables()) {
            $this->error('❌ Les tables Orchid ne sont pas présentes dans la base de données');
            return Command::FAILURE;
        }

        // Vérification et création des tables Spatie si nécessaire
        if (!$this->ensureSpatieTablesExist($isDryRun)) {
            $this->error('❌ Impossible de créer les tables Spatie');
            return Command::FAILURE;
        }

        // Renommer la colonne permissions d'Orchid pour éviter les conflits avec Spatie
        if (!$this->renameOrchidPermissionsColumn($isDryRun)) {
            $this->error('❌ Impossible de renommer la colonne permissions');
            return Command::FAILURE;
        }

        // Confirmation avant migration
        if (!$isDryRun && !$this->option('force')) {
            if (!$this->confirm('Voulez-vous continuer la migration ?', true)) {
                $this->info('Migration annulée');
                return Command::SUCCESS;
            }
        }

        try {
            DB::beginTransaction();

            // 1. Migration des rôles
            $this->migrateRoles($isDryRun);
            
            // 2. Migration des permissions
            $this->migratePermissions($isDryRun);
            
            // 2b. Ajout des permissions supplémentaires
            $this->createAdditionalPermissions($isDryRun);
            
            // 3. Association rôles-permissions
            $this->migrateRolePermissions($isDryRun);
            
            // 4. Migration des utilisateurs
            $this->migrateUsers($isDryRun);

            if (!$isDryRun) {
                DB::commit();
                $this->info('✅ Transaction validée');
            } else {
                DB::rollBack();
                $this->info('✅ Simulation terminée - aucune modification effectuée');
            }

            $this->displayStats();

            // Nettoyage post-migration
            if (!$isDryRun && $this->shouldCleanup()) {
                $this->newLine();
                if ($this->cleanupOrchidData()) {
                    $this->info('🧹 Nettoyage terminé avec succès');
                }
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Erreur lors de la migration : ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return Command::FAILURE;
        }
    }

    /**
     * Vérifie la présence des tables Orchid
     */
    private function checkOrchidTables(): bool
    {
        return DB::getSchemaBuilder()->hasTable('role_users');
    }

    /**
     * Renomme la colonne permissions d'Orchid pour éviter les conflits avec Spatie
     */
    private function renameOrchidPermissionsColumn(bool $isDryRun): bool
    {
        $this->info('🔄 Vérification de la colonne permissions...');
        
        $schema = DB::getSchemaBuilder();
        
        // Vérifier si la colonne permissions existe
        if (!$schema->hasColumn('users', 'permissions')) {
            $this->info('  ℹ️  La colonne permissions n\'existe pas dans la table users');
            $this->newLine();
            return true;
        }

        // Vérifier si la colonne a déjà été renommée
        if ($schema->hasColumn('users', 'orchid_permissions')) {
            $this->info('  ✅ La colonne a déjà été renommée en orchid_permissions');
            $this->newLine();
            return true;
        }

        if ($isDryRun) {
            $this->warn('  ⚠️  En mode réel, la colonne "permissions" sera renommée en "orchid_permissions"');
            $this->newLine();
            return true;
        }

        try {
            $this->info('  ⏳ Renommage de la colonne permissions → orchid_permissions...');
            
            // Utiliser une requête SQL brute pour plus de compatibilité
            DB::statement('ALTER TABLE users CHANGE permissions orchid_permissions LONGTEXT NULL');
            
            $this->info('  ✅ Colonne renommée avec succès');
            $this->newLine();
            
            return true;
            
        } catch (\Exception $e) {
            $this->error('  ❌ Erreur lors du renommage : ' . $e->getMessage());
            $this->newLine();
            return false;
        }
    }

    /**
     * Vérifie et crée les tables Spatie si nécessaire
     */
    private function ensureSpatieTablesExist(bool $isDryRun): bool
    {
        $this->info('🔍 Vérification des tables Spatie...');
        
        // Récupérer les noms de tables depuis la configuration
        $tableNames = config('permission.table_names', [
            'roles' => 'roles',
            'permissions' => 'permissions',
            'model_has_permissions' => 'model_has_permissions',
            'model_has_roles' => 'model_has_roles',
            'role_has_permissions' => 'role_has_permissions',
        ]);

        $requiredTables = [
            $tableNames['permissions'],
            $tableNames['roles'],
            $tableNames['model_has_permissions'],
            $tableNames['model_has_roles'],
            $tableNames['role_has_permissions'],
        ];

        $missingTables = [];
        
        foreach ($requiredTables as $table) {
            if (!DB::getSchemaBuilder()->hasTable($table)) {
                $missingTables[] = $table;
            }
        }

        if (empty($missingTables)) {
            $this->info('  ✅ Toutes les tables Spatie sont présentes');
            $this->newLine();
            return true;
        }

        $this->warn('  ⚠️  Tables Spatie manquantes : ' . implode(', ', $missingTables));
        $this->newLine();

        if ($isDryRun) {
            $this->info('  ℹ️  En mode simulation, les tables ne seront pas créées');
            $this->newLine();
            return true;
        }

        // Demander confirmation pour créer les tables
        if (!$this->option('force')) {
            if (!$this->confirm('Voulez-vous créer automatiquement les tables Spatie ?', true)) {
                $this->error('Migration annulée. Veuillez exécuter : php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"');
                $this->error('Puis : php artisan migrate');
                return false;
            }
        }

        return $this->createSpatieTables();
    }

    /**
     * Crée les tables Spatie Permission
     */
    private function createSpatieTables(): bool
    {
        $this->info('📦 Création des tables Spatie...');
        
        try {
            // Récupérer le nom de la table configurée ou utiliser les valeurs par défaut
            $tableNames = config('permission.table_names', [
                'roles' => 'roles',
                'permissions' => 'permissions',
                'model_has_permissions' => 'model_has_permissions',
                'model_has_roles' => 'model_has_roles',
                'role_has_permissions' => 'role_has_permissions',
            ]);

            $columnNames = config('permission.column_names', [
                'role_pivot_key' => null,
                'permission_pivot_key' => null,
                'model_morph_key' => 'model_id',
                'team_foreign_key' => 'team_id',
            ]);

            $teams = config('permission.teams', false);

            $schema = DB::getSchemaBuilder();

            // Table permissions
            if (!$schema->hasTable($tableNames['permissions'])) {
                $schema->create($tableNames['permissions'], function ($table) use ($teams, $columnNames) {
                    $table->bigIncrements('id');
                    if ($teams || config('permission.testing')) {
                        $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                        $table->index($columnNames['team_foreign_key'], 'permissions_team_foreign_key_index');
                    }
                    $table->string('name');
                    $table->string('guard_name');
                    $table->timestamps();

                    if ($teams || config('permission.testing')) {
                        $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
                    } else {
                        $table->unique(['name', 'guard_name']);
                    }
                });
                $this->info('  ✅ Table ' . $tableNames['permissions'] . ' créée');
            }

            // Table roles
            if (!$schema->hasTable($tableNames['roles'])) {
                $schema->create($tableNames['roles'], function ($table) use ($teams, $columnNames) {
                    $table->bigIncrements('id');
                    if ($teams || config('permission.testing')) {
                        $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable();
                        $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
                    }
                    $table->string('name');
                    $table->string('guard_name');
                    $table->timestamps();

                    if ($teams || config('permission.testing')) {
                        $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
                    } else {
                        $table->unique(['name', 'guard_name']);
                    }
                });
                $this->info('  ✅ Table ' . $tableNames['roles'] . ' créée');
            }

            // Table model_has_permissions
            if (!$schema->hasTable($tableNames['model_has_permissions'])) {
                $schema->create($tableNames['model_has_permissions'], function ($table) use ($tableNames, $columnNames, $teams) {
                    $table->unsignedBigInteger($columnNames['permission_pivot_key'] ?: 'permission_id');

                    $table->string('model_type');
                    $table->unsignedBigInteger($columnNames['model_morph_key']);
                    $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

                    $table->foreign($columnNames['permission_pivot_key'] ?: 'permission_id')
                        ->references('id')
                        ->on($tableNames['permissions'])
                        ->onDelete('cascade');

                    if ($teams) {
                        $table->unsignedBigInteger($columnNames['team_foreign_key']);
                        $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                        $table->primary(
                            [$columnNames['team_foreign_key'], $columnNames['permission_pivot_key'] ?: 'permission_id', $columnNames['model_morph_key'], 'model_type'],
                            'model_has_permissions_permission_model_type_primary'
                        );
                    } else {
                        $table->primary(
                            [$columnNames['permission_pivot_key'] ?: 'permission_id', $columnNames['model_morph_key'], 'model_type'],
                            'model_has_permissions_permission_model_type_primary'
                        );
                    }
                });
                $this->info('  ✅ Table ' . $tableNames['model_has_permissions'] . ' créée');
            }

            // Table model_has_roles
            if (!$schema->hasTable($tableNames['model_has_roles'])) {
                $schema->create($tableNames['model_has_roles'], function ($table) use ($tableNames, $columnNames, $teams) {
                    $table->unsignedBigInteger($columnNames['role_pivot_key'] ?: 'role_id');

                    $table->string('model_type');
                    $table->unsignedBigInteger($columnNames['model_morph_key']);
                    $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

                    $table->foreign($columnNames['role_pivot_key'] ?: 'role_id')
                        ->references('id')
                        ->on($tableNames['roles'])
                        ->onDelete('cascade');

                    if ($teams) {
                        $table->unsignedBigInteger($columnNames['team_foreign_key']);
                        $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                        $table->primary(
                            [$columnNames['team_foreign_key'], $columnNames['role_pivot_key'] ?: 'role_id', $columnNames['model_morph_key'], 'model_type'],
                            'model_has_roles_role_model_type_primary'
                        );
                    } else {
                        $table->primary(
                            [$columnNames['role_pivot_key'] ?: 'role_id', $columnNames['model_morph_key'], 'model_type'],
                            'model_has_roles_role_model_type_primary'
                        );
                    }
                });
                $this->info('  ✅ Table ' . $tableNames['model_has_roles'] . ' créée');
            }

            // Table role_has_permissions
            if (!$schema->hasTable($tableNames['role_has_permissions'])) {
                $schema->create($tableNames['role_has_permissions'], function ($table) use ($tableNames, $columnNames) {
                    $table->unsignedBigInteger($columnNames['permission_pivot_key'] ?: 'permission_id');
                    $table->unsignedBigInteger($columnNames['role_pivot_key'] ?: 'role_id');

                    $table->foreign($columnNames['permission_pivot_key'] ?: 'permission_id')
                        ->references('id')
                        ->on($tableNames['permissions'])
                        ->onDelete('cascade');

                    $table->foreign($columnNames['role_pivot_key'] ?: 'role_id')
                        ->references('id')
                        ->on($tableNames['roles'])
                        ->onDelete('cascade');

                    $table->primary(
                        [$columnNames['permission_pivot_key'] ?: 'permission_id', $columnNames['role_pivot_key'] ?: 'role_id'],
                        'role_has_permissions_permission_id_role_id_primary'
                    );
                });
                $this->info('  ✅ Table ' . $tableNames['role_has_permissions'] . ' créée');
            }

            $this->info('  ✅ Toutes les tables Spatie ont été créées avec succès');
            $this->newLine();

            // Vider le cache des permissions
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            return true;

        } catch (\Exception $e) {
            $this->error('  ❌ Erreur lors de la création des tables : ' . $e->getMessage());
            $this->newLine();
            return false;
        }
    }

    /**
     * Migre les rôles d'Orchid vers Spatie
     */
    private function migrateRoles(bool $isDryRun): void
    {
        $this->info('📋 Migration des rôles...');
        
        // Vérifier si la table roles existe (structure Orchid standard)
        if (!DB::getSchemaBuilder()->hasTable('roles')) {
            $this->warn('  ⚠️  Table "roles" non trouvée. Aucun rôle à migrer.');
            $this->newLine();
            return;
        }

        // Récupérer tous les rôles depuis la table roles d'Orchid
        $orchidRoles = DB::table('roles')->get();

        if ($orchidRoles->isEmpty()) {
            $this->warn('  ⚠️  Aucun rôle trouvé dans la table "roles".');
            $this->newLine();
            return;
        }

        $progressBar = $this->output->createProgressBar($orchidRoles->count());
        $progressBar->start();

        foreach ($orchidRoles as $orchidRole) {
            // Orchid utilise généralement les champs : id, slug, name, permissions
            $roleName = $orchidRole->slug ?? $orchidRole->name ?? 'role_' . $orchidRole->id;
            
            if (!$isDryRun) {
                $role = Role::firstOrCreate(
                    ['name' => $roleName],
                    ['guard_name' => 'web']
                );
                
                // Si Orchid stocke aussi des permissions dans le rôle, les migrer
                if (isset($orchidRole->permissions) && !empty($orchidRole->permissions)) {
                    $rolePermissions = json_decode($orchidRole->permissions, true);
                    if (is_array($rolePermissions)) {
                        $activePermissions = array_keys(array_filter($rolePermissions));
                        if (!empty($activePermissions)) {
                            // Les permissions seront créées plus tard, on les associera après
                            $this->rolePermissionsMap[$roleName] = $activePermissions;
                        }
                    }
                }
                
                $this->stats['roles_created']++;
            } else {
                $this->stats['roles_created']++;
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
    }

    /**
     * Migre les permissions d'Orchid vers Spatie
     */
    private function migratePermissions(bool $isDryRun): void
    {
        $this->info('🔐 Migration des permissions...');
        
        // Récupère toutes les permissions uniques depuis les users
        $allPermissions = collect();
        
        // En mode dry-run, la colonne n'a pas encore été renommée
        $permissionsColumn = $isDryRun ? 'permissions' : 'orchid_permissions';
        
        $users = DB::table('users')
            ->whereNotNull($permissionsColumn)
            ->get([$permissionsColumn]);

        foreach ($users as $user) {
            $permissions = json_decode($user->$permissionsColumn, true);
            if (is_array($permissions)) {
                foreach ($permissions as $permission => $value) {
                    if ($value == 1 || $value === true || $value === "1") {
                        $allPermissions->push($permission);
                    }
                }
            }
        }

        // Ajouter aussi les permissions des rôles
        if (DB::getSchemaBuilder()->hasTable('roles')) {
            $roles = DB::table('roles')->get();
            foreach ($roles as $role) {
                if ($role->permissions) {
                    $rolePermissions = json_decode($role->permissions, true);
                    if (is_array($rolePermissions)) {
                        foreach ($rolePermissions as $permission => $value) {
                            if ($value == 1 || $value === true || $value === "1") {
                                $allPermissions->push($permission);
                            }
                        }
                    }
                }
            }
        }

        $uniquePermissions = $allPermissions->unique();
        
        $progressBar = $this->output->createProgressBar($uniquePermissions->count());
        $progressBar->start();

        foreach ($uniquePermissions as $permissionName) {
            if (!$isDryRun) {
                Permission::firstOrCreate(
                    ['name' => $permissionName],
                    ['guard_name' => 'web']
                );
                $this->stats['permissions_created']++;
            } else {
                $this->stats['permissions_created']++;
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
    }

    /**
     * Crée des permissions supplémentaires spécifiques
     */
    private function createAdditionalPermissions(bool $isDryRun): void
    {
        $this->info('➕ Ajout de permissions supplémentaires...');
        
        $additionalPermissions = [
            'platform.quality.criterias',
            'platform.quality.criteria.create',
            'platform.quality.criteria.edit',
            'platform.quality.stages',
            'platform.quality.stage.create',
            'platform.quality.stage.edit',
            'platform.quality.wealth_types',
            'platform.quality.wealth_type.create',
            'platform.quality.wealth_type.edit',
            'platform.systems.units',
        ];

        $created = 0;
        $alreadyExists = 0;

        foreach ($additionalPermissions as $permissionName) {
            if (!$isDryRun) {
                $permission = Permission::firstOrCreate(
                    ['name' => $permissionName],
                    ['guard_name' => 'web']
                );
                
                if ($permission->wasRecentlyCreated) {
                    $created++;
                    $this->stats['permissions_created']++;
                } else {
                    $alreadyExists++;
                }
            } else {
                $created++;
                $this->stats['permissions_created']++;
            }
        }

        $this->info("  ✅ {$created} permission(s) supplémentaire(s) créée(s)");
        if ($alreadyExists > 0) {
            $this->info("  ℹ️  {$alreadyExists} permission(s) existait(ent) déjà");
        }
        $this->newLine();
    }

    /**
     * Associe les permissions aux rôles
     */
    private function migrateRolePermissions(bool $isDryRun): void
    {
        $this->info('🔗 Association rôles-permissions...');
        
        if (empty($this->rolePermissionsMap)) {
            $this->warn('  ⚠️  Aucune permission définie au niveau des rôles dans Orchid.');
            $this->info('  ℹ️  Les permissions seront héritées via les assignations directes aux utilisateurs.');
            $this->newLine();
            return;
        }
        
        if (!$isDryRun) {
            foreach ($this->rolePermissionsMap as $roleName => $permissionNames) {
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    $permissions = Permission::whereIn('name', $permissionNames)->get();
                    $role->syncPermissions($permissions);
                    $this->info("  → Rôle '{$roleName}' : " . count($permissions) . " permissions associées");
                }
            }
        } else {
            foreach ($this->rolePermissionsMap as $roleName => $permissionNames) {
                $this->info("  → Rôle '{$roleName}' : " . count($permissionNames) . " permissions à associer");
            }
        }
        
        $this->newLine();
    }

    /**
     * Migre les utilisateurs et leurs permissions/rôles
     */
    private function migrateUsers(bool $isDryRun): void
    {
        $this->info('👥 Migration des utilisateurs...');
        
        $users = User::all();
        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();

        foreach ($users as $user) {
            try {
                // Migration des rôles depuis role_users (avec role_id)
                $userRoleIds = DB::table('role_users')
                    ->where('user_id', $user->id)
                    ->pluck('role_id')
                    ->toArray();

                if (!empty($userRoleIds)) {
                    // Récupérer les noms des rôles depuis la table roles d'Orchid
                    $orchidRoles = DB::table('roles')
                        ->whereIn('id', $userRoleIds)
                        ->get();
                    
                    $roleNames = [];
                    foreach ($orchidRoles as $orchidRole) {
                        $roleNames[] = $orchidRole->slug ?? $orchidRole->name ?? 'role_' . $orchidRole->id;
                    }

                    if (!$isDryRun && !empty($roleNames)) {
                        $user->syncRoles($roleNames);
                    }
                }

                // Migration des permissions directes depuis orchid_permissions (ou permissions en mode dry-run)
                $permissionsColumn = $isDryRun ? 'permissions' : 'orchid_permissions';
                $orchidPermissionsJson = DB::table('users')
                    ->where('id', $user->id)
                    ->value($permissionsColumn);

                if ($orchidPermissionsJson) {
                    $permissions = json_decode($orchidPermissionsJson, true);
                    if (is_array($permissions)) {
                        $activePermissions = array_keys(array_filter($permissions, function($value) {
                            // Orchid utilise "1" ou 1 pour les permissions actives
                            return $value == 1 || $value === true || $value === "1";
                        }));
                        
                        if (!$isDryRun && !empty($activePermissions)) {
                            $user->syncPermissions($activePermissions);
                        }
                    }
                }

                $this->stats['users_migrated']++;
                
            } catch (\Exception $e) {
                $this->stats['errors'][] = "User {$user->id}: " . $e->getMessage();
            }
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
    }

    /**
     * Affiche les statistiques de migration
     */
    private function displayStats(): void
    {
        $this->newLine();
        $this->info('📊 Statistiques de migration :');
        $this->table(
            ['Élément', 'Nombre'],
            [
                ['Rôles créés', $this->stats['roles_created']],
                ['Permissions créées', $this->stats['permissions_created']],
                ['Utilisateurs migrés', $this->stats['users_migrated']],
                ['Erreurs', count($this->stats['errors'])],
            ]
        );

        if (!empty($this->stats['errors'])) {
            $this->newLine();
            $this->error('⚠️  Erreurs rencontrées :');
            foreach ($this->stats['errors'] as $error) {
                $this->error('  • ' . $error);
            }
        }
    }

    /**
     * Détermine si le nettoyage doit être effectué
     */
    private function shouldCleanup(): bool
    {
        // Si l'option --cleanup est activée
        if ($this->option('cleanup')) {
            return true;
        }

        // Sinon, demander confirmation
        return $this->confirm('Voulez-vous nettoyer les anciennes tables et colonnes Orchid ?', false);
    }

    /**
     * Nettoie les données Orchid (tables et colonnes)
     */
    private function cleanupOrchidData(): bool
    {
        $this->newLine();
        $this->info('🧹 Nettoyage des données Orchid...');
        
        $keepBackup = $this->option('keep-backup');
        $schema = DB::getSchemaBuilder();

        try {
            // 1. Gestion de la colonne orchid_permissions
            if ($schema->hasColumn('users', 'orchid_permissions')) {
                if ($keepBackup) {
                    $this->info('  ℹ️  Conservation de la colonne orchid_permissions (--keep-backup activé)');
                } else {
                    $this->info('  ⏳ Suppression de la colonne orchid_permissions...');
                    DB::statement('ALTER TABLE users DROP COLUMN orchid_permissions');
                    $this->info('  ✅ Colonne orchid_permissions supprimée');
                }
            }

            // 2. Gestion de la table role_users
            if ($schema->hasTable('role_users')) {
                if ($keepBackup) {
                    $this->info('  ⏳ Sauvegarde de la table role_users → role_users_backup...');
                    // Supprimer l'ancienne backup si elle existe
                    if ($schema->hasTable('role_users_backup')) {
                        DB::statement('DROP TABLE role_users_backup');
                    }
                    DB::statement('RENAME TABLE role_users TO role_users_backup');
                    $this->info('  ✅ Table role_users sauvegardée');
                } else {
                    $this->info('  ⏳ Suppression de la table role_users...');
                    DB::statement('DROP TABLE role_users');
                    $this->info('  ✅ Table role_users supprimée');
                }
            }

            // 3. Gestion de la table roles
            if ($schema->hasTable('roles')) {
                if ($keepBackup) {
                    $this->info('  ⏳ Sauvegarde de la table roles → roles_orchid_backup...');
                    // Supprimer l'ancienne backup si elle existe
                    if ($schema->hasTable('roles_orchid_backup')) {
                        DB::statement('DROP TABLE roles_orchid_backup');
                    }
                    DB::statement('RENAME TABLE roles TO roles_orchid_backup');
                    $this->info('  ✅ Table roles sauvegardée');
                } else {
                    $this->info('  ⏳ Suppression de la table roles...');
                    DB::statement('DROP TABLE roles');
                    $this->info('  ✅ Table roles supprimée');
                }
            }

            $this->newLine();
            
            if ($keepBackup) {
                $this->info('💾 Les données Orchid ont été conservées en backup :');
                $this->info('   • users.orchid_permissions (colonne conservée)');
                if ($schema->hasTable('role_users_backup')) {
                    $this->info('   • role_users_backup (table)');
                }
                if ($schema->hasTable('roles_orchid_backup')) {
                    $this->info('   • roles_orchid_backup (table)');
                }
                $this->newLine();
                $this->warn('⚠️  Pour supprimer définitivement ces backups, relancez sans --keep-backup');
            } else {
                $this->info('✨ Nettoyage terminé ! Toutes les données Orchid ont été supprimées.');
            }

            return true;

        } catch (\Exception $e) {
            $this->error('❌ Erreur lors du nettoyage : ' . $e->getMessage());
            return false;
        }
    }
}
