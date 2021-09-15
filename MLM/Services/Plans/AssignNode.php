<?php
namespace MLM\Services\Plans;

use Illuminate\Support\Collection;
use MLM\Interfaces\Plan;

class AssignNode implements Plan
{

    public function getCommissions(): ?Collection
    {
        return Collection::make([

        ]);
    }
    public function getName(): string
    {
        return 'assign-node';
    }

    public function backupPlan(): ?Plan
    {
        return null;
    }
}
