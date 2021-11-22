<?php

namespace User\Convert;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\In;
use MLM\Models\ReferralTree;
use MLM\Models\Tree;
use User\Convert\Models\Individual;
use User\Models\User;

class ConvertCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $count = Individual::query()->count();
        $this->info(PHP_EOL . 'number of user rows ' . $count . PHP_EOL);

        $bar = $this->output->createProgressBar($count);

        $this->info(PHP_EOL . 'Start user conversion');
        $bar->start();

        Individual::with('detail')->
        chunk(2000, function ($users) use ($bar) {
            $last_users = [];
            $last_insert_referral = [];
            $last_insert_binary = [];
            foreach ($users as $item) {
                $last_users[] = ['id' => $item->id, 'rank' => $this->rankConvert($item->user_rank_id)];

                if ($item->sponsor_id) {
                    $last_insert_referral[] = ['id' => $item->id, 'parent_id' => $item->sponsor_id, 'user_id' => $item->id];
                }
                if ($item->father_id && $item->active == "yes") {

                    if ($item->position == "L")
                        $last_insert_binary[] = [
                            'id' => $item->id,
                            'parent_id' => $item->father_id,
                            'user_id' => $item->id,
                            'position' => 'left'
                        ];
                    else
                        $last_insert_binary[] = [
                            'id' => $item->id,
                            'parent_id' => $item->father_id,
                            'user_id' => $item->id,
                            'position' => 'right'
                        ];
                }
                $bar->advance();
            }
            $insert_data = collect($last_users);
            $chunks = $insert_data->chunk(500);

            foreach ($chunks as $chunk) {
                DB::table('users')->insert($chunk->toArray());

            }
            $insert_data = collect($last_insert_binary);
            $chunks = $insert_data->chunk(500);

            foreach ($chunks as $chunk) {
                DB::table('trees')->insert($chunk->toArray());

            }
            $insert_data = collect($last_insert_referral);
            $chunks = $insert_data->chunk(500);

            foreach ($chunks as $chunk) {
                DB::table('referral_trees')->insert($chunk->toArray());

            }
        });
        $bar->finish();
        $this->info(PHP_EOL . 'User Conversion Finished' . PHP_EOL);

        ini_set('memory_limit', '-1');
        $this->info(PHP_EOL . 'Fixing Trees Started' . PHP_EOL);
        $bar = $this->output->createProgressBar(2);
        Tree::withoutEvents(function () {
            Tree::fixTree();
        });
        $bar->advance();
        ReferralTree::withoutEvents(function () {
            ReferralTree::fixTree();
        });
        $bar->advance();
        $this->info(PHP_EOL . ' Finished' . PHP_EOL);
    }

    public function rankConvert($rank)
    {
        $output_rank = 0;
        switch ((int)$rank) {
            case 1:
                $output_rank = 2;
                break;
            case 2:
                $output_rank = 3;
                break;
            case 3:
                $output_rank = 4;
                break;
            case 4:
                $output_rank = 6;
                break;
            case 5:
                $output_rank = 7;
                break;
            case 6:
                $output_rank = 8;
                break;
            case 7:
                $output_rank = 9;
                break;
            case 8:
                $output_rank = 10;
                break;
            case 9:
                $output_rank = 11;
                break;
            case 10:
                $output_rank = 12;
                break;
        }
        return $output_rank;
    }


}
