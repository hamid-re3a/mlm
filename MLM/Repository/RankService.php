<?php


namespace MLM\Repository;

use Illuminate\Support\Facades\Log;
use MLM\Models\Rank;

class RankService
{

    protected $entity_name = Rank::class;


    public function getAll()
    {
        /** @var $rank_model Rank */
        try {
            $rank_model = new $this->entity_name;
            return $rank_model->all();
        } catch (\Throwable $exception) {
            Log::error('RankService@fetch all error => ' . $exception->getMessage());
            throw $exception;
        }
    }

    public function getById(int $id)
    {
        /** @var $rank_model Rank */
        try {
            $rank_model = new $this->entity_name;
            $rank_model = $rank_model->query()->where('id', '=', $id)->first();
            if (!$rank_model)
                throw new \Exception(trans('mlm.responses.ranks.wrong-id'));

            return $rank_model;


        } catch (\Throwable $exception) {
            Log::error('RankService@fetch ');
            throw $exception;
        }
    }

    public function create(array $data)
    {
        try {
            /** @var $rank_model Rank */
            $rank_model = new $this->entity_name;
            return $rank_model->query()->create([
                'rank' => $data['rank'],
                'rank_name' => $data['rank_name'],
                'condition_converted_in_bp' => isset($data['condition_converted_in_bp']) ? $data['condition_converted_in_bp'] : 0,
                'condition_sub_rank' => isset($data['condition_sub_rank']) ? $data['condition_sub_rank'] : 0,
                'condition_direct_or_indirect' => $data['condition_direct_or_indirect'],
                'prize_in_pf' => isset($data['prize_in_pf']) ? $data['prize_in_pf'] : null,
                'prize_alternative' => isset($data['prize_alternative']) ? $data['prize_alternative'] : null,
                'cap' => $data['cap'],
                'withdrawal_limit' => $data['withdrawal_limit'],
                'condition_number_of_left_children' => $data['condition_number_of_left_children'],
                'condition_number_of_right_children' => $data['condition_number_of_right_children'],
            ]);
        } catch (\Throwable $exception) {
            Log::error('RankService@create DATA =>' . serialize($data));
            throw $exception;
        }
    }

    public function update(array $data)
    {

        try {
            /** @var $rank_model Rank */
            $rank_model = new $this->entity_name;
            $rank_model = $rank_model->query()->where('id', '=', $data['id'])->first();
            if (!$rank_model)
                throw new \Exception(trans('mlm.responses.ranks.wrong-id'));

            $rank_model->update([
                'rank' => $data['rank'],
                'rank_name' => $data['rank_name'],
                'condition_converted_in_bp' => isset($data['condition_converted_in_bp']) ? $data['condition_converted_in_bp'] : 0,
                'condition_sub_rank' => isset($data['condition_sub_rank']) ? $data['condition_sub_rank'] : 0,
                'condition_direct_or_indirect' => $data['condition_direct_or_indirect'],
                'prize_in_pf' => isset($data['prize_in_pf']) ? $data['prize_in_pf'] : null,
                'prize_alternative' => isset($data['prize_alternative']) ? $data['prize_alternative'] : null,
                'cap' => $data['cap'],
                'withdrawal_limit' => $data['withdrawal_limit'],
                'condition_number_of_left_children' => $data['condition_number_of_left_children'],
                'condition_number_of_right_children' => $data['condition_number_of_right_children'],
            ]);

            return $rank_model->fresh();

        } catch (\Throwable $exception) {
            Log::error('RankService@update  DATA => ' . serialize($data));
            throw $exception;
        }

    }

    public function delete(int $id)
    {
        try {
            /** @var $rank_model Rank */
            $rank_model = new $this->entity_name;
            $rank_model = $rank_model->query()->where('id', '=', $id)->first();
            if (!$rank_model)
                throw new \Exception(trans('mlm.responses.ranks.wrong-id'));

            if ($rank_model->residualBonusSettings()->count())
                throw new \Exception(trans('mlm.responses.has-residual-bonus-settings-you-cant-delete'));

            $rank_model->delete();
            return true;

        } catch (\Throwable $exception) {
            Log::error('RankService@delete ID => ' . $id);
            throw $exception;
        }
    }
}
