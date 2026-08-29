<?php

namespace Modules\MigraineDiary\App\Permissions;

use App\Contracts\ModulePermissions;
use App\Models\Role;

/**
 * Declares Module permissions
 */
final class MigraineDiaryPermissions implements ModulePermissions
{
    /**
     * @return array
     */
    public static function all(): array
    {
        return [
            'access',
            'view',
            'create',
            'update',
            'delete',
        ];
    }

    /**
     * @return array
     */
    public static function defaults(): array
    {
        return [
            Role::ADMIN => [
                'access' => true,
                'view' => true,
                'create' => true,
                'update' => true,
                'delete' => true,
            ],

            Role::USER => [
                'access' => true,
                'view' => true,
                'create' => true,
                'update' => true,
                'delete' => true,
            ],
        ];
    }
}
