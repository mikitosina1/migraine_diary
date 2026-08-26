<?php

namespace Modules\MigraineDiary\App\Permissions;

use App\Contracts\ModulePermissions;

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
            config('roles.admin') => [
                'access' => true,
                'view' => true,
                'create' => true,
                'update' => true,
                'delete' => true,
            ],

            config('roles.user') => [
                'access' => true,
                'view' => true,
                'create' => true,
                'update' => true,
                'delete' => true,
            ],
        ];
    }
}