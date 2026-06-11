<?php

namespace Modules\MigraineDiary\App\Actions\Admin;

use Illuminate\Database\Eloquent\Model;
use Modules\MigraineDiary\App\Services\Admin\EntityService;

class ShowDictionaryEntityAction
{
    public function __construct(
        private readonly EntityService $entities,
    ) {}

    public function execute(string $type, int $id): Model
    {
        return $this->entities->findEntity($type, $id);
    }
}
