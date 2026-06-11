<?php

namespace Modules\MigraineDiary\App\Actions\Admin;

use Illuminate\Support\Collection;
use Modules\MigraineDiary\App\Services\Admin\EntityService;

class ListDictionaryEntitiesAction
{
    public function __construct(
        private readonly EntityService $entities,
    ) {}

    public function execute(string $type): Collection
    {
        return $this->entities->listEntities($type);
    }
}
