<?php
namespace MLM\Interfaces;


use Orders\Services\Order;

interface Commission {

    public function calculate(Order $order) : bool;

    public function backupCommissionPlan() : ?Commission;

    public function getType(): string;
}
