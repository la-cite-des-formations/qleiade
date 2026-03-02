<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Permissions dédiées pour les ressources Criteria, Stage, WealthType et Unit.
     *
     * @var array<int, string>
     */
    private const PERMISSIONS = [
        // Criteria
        'platform.quality.criterias',
        'platform.quality.criteria.create',
        'platform.quality.criteria.edit',

        // Stage
        'platform.quality.stages',
        'platform.quality.stage.create',
        'platform.quality.stage.edit',

        // WealthType
        'platform.quality.wealth_types',
        'platform.quality.wealth_type.create',
        'platform.quality.wealth_type.edit',

        // Unit
        'platform.systems.units',
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }
    }
}
