<?php

namespace Modules\MigraineDiary\App\Actions\Admin;

use Modules\MigraineDiary\App\Services\Admin\EntityService;

class ListAllDictionaryEntitiesAction
{
    public function __construct(
        private readonly EntityService $entities,
    ) {}

    public function execute(): array
    {
        return $this->entities->listAll();
    }
}
