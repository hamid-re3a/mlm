<?php
namespace MLM\Interfaces;

use Illuminate\Support\Collection;

interface Plan {

    /**
     * @return Commission[]|null
     */
    public function getCommissions() : ?Collection;

    public function getName() : string;

    public function backupPlan(): ?Plan;

}
