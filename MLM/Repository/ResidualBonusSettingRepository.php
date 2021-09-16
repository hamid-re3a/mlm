<?php


namespace MLM\Repository;

use MLM\Models\ResidualBonusSetting;
use MLM\Services\Grpc\ResidualBonus as ResidualBonusData;

class ResidualBonusSettingRepository
{
    protected $entity_name = ResidualBonusSetting::class;

    public function create(ResidualBonusData $residualBonusData)
    {
        $residualBonus_entity = new $this->entity_name;
        $residualBonus = $residualBonus_entity->create([
            "rank" => $residualBonusData->getRank(),
            "level" => $residualBonusData->getLevel(),
            "percentage" => $residualBonusData->getPercentage(),
        ]);
        $residualBonus = $residualBonus->fresh();

        return $residualBonus;
    }

    public function update(ResidualBonusData $residualBonusData)
    {
        $residualBonus_entity = new $this->entity_name;

        $residualBonus_find = $residualBonus_entity->query()->firstOrCreate(
            [
                'id' => $residualBonusData->getId(),
            ],
            [
                "rank" => $residualBonusData->getRank(),
                "level" => $residualBonusData->getLevel(),
                "percentage" => $residualBonusData->getPercentage(),
            ]
        );
        $residualBonus_find->update([
            "rank" => $residualBonusData->getRank(),
            "level" => $residualBonusData->getLevel(),
            "percentage" => $residualBonusData->getPercentage(),

        ]);

        return $residualBonus_find;

    }

    public function delete(int $id)
    {
        $residualBonus_entity = new $this->entity_name;
        $residualBonus_find = $residualBonus_entity->query()->find($id);
        $residualBonus_find->delete();

    }

    public function getAll()
    {
        $residualBonus_entity = new $this->entity_name;

        return $residualBonus_entity->query()->with('rankCollection')->get();

    }

    public function getById(int $id)
    {
        $residualBonus_entity = new $this->entity_name;

        return $residualBonus_entity->query()->with('rankCollection')->find($id);

    }

}
