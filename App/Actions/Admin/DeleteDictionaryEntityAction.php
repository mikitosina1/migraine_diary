<?php

namespace Modules\MigraineDiary\App\Actions\Admin;

use Modules\MigraineDiary\App\Services\Admin\EntityService;

class DeleteDictionaryEntityAction
{
    public function __construct(
        private readonly EntityService $entities,
    ) {}

    public function execute(string $type, int $id): void
    {
        $this->entities->deleteEntity($type, $id);
    }
}
