<?php

namespace Modules\MigraineDiary\App\Actions\Admin;

use Illuminate\Database\Eloquent\Model;
use Modules\MigraineDiary\App\Services\Admin\EntityService;

class CreateDictionaryEntityAction
{
    public function __construct(
        private readonly EntityService $entities,
    ) {}

    /**
     * @param  string  $type  symptoms | meds | triggers
     */
    public function execute(string $type, array $data): Model
    {
        return $this->entities->createEntity($type, $data);
    }
}
