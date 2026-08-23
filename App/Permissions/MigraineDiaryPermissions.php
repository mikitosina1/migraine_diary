<?php

namespace Modules\MigraineDiary\App\Permissions;

use App\Contracts\ModulePermissions;

final class MigraineDiaryPermissions implements ModulePermissions
{
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
}