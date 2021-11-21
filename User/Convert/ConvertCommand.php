<?php

namespace User\Convert;

use Carbon\Carbon;
use Illuminate\Console\Command;
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
        $users = Individual::with('detail')->
        chunk(50, function ($users) use ($bar) {

            foreach ($users as $item) {
                $current_user = User::query()->find($item->id);
                if (!$current_user)
                    $current_user = User::factory()->create(['id' => $item->id]);

                $current_user->rank = $this->rankConvert($item->user_rank_id);
                $current_user->saveQuietly();
                $current_user->assignRole(USER_ROLE_CLIENT);

                    if ($item->sponsor_id) {
                        $sponsor = User::query()->firstOrCreate(['id' => $item->sponsor_id]);
                        $sponsor_tree = $sponsor->buildReferralTreeNode();
                        ReferralTree::query()->firstOrCreate(['parent_id' => $sponsor_tree->id, 'user_id' => $current_user->id]);
//                        $sponsor_tree->appendNode($current_user->buildReferralTreeNode());
                    } 
                    if ($item->father_id && $item->active == "yes") {
                        $parent = User::query()->firstOrCreate(['id' => $item->father_id]);
                        if (is_null($parent->binaryTree))
                            $parent->binaryTree()->create();
//                        if (is_null($current_user->binaryTree))
//                            $current_user->binaryTree()->create();

                        $parent->refresh();
//                        $current_user->refresh();

                        if ($item->position == "L")
                            Tree::query()->firstOrCreate([
                                'parent_id' => $parent->binaryTree->id,
                                'user_id' => $current_user->id,
                                'position' => 'left'
                            ]);
//                            $parent->binaryTree->appendAsLeftNode($current_user->binaryTree);
                        else
                            Tree::query()->firstOrCreate([
                                'parent_id' => $parent->binaryTree->id,
                                'user_id' => $current_user->id,
                                'position' => 'right'
                            ]);
//                            $parent->binaryTree->appendAsRightNode($current_user->binaryTree);
                    }



                $bar->advance();
            }


        });
        Tree::withoutEvents(function(){Tree::fixTree();});
        ReferralTree::withoutEvents(function(){ReferralTree::fixTree();});
        $bar->finish();
        $this->info(PHP_EOL . 'User Conversion Finished' . PHP_EOL);
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
