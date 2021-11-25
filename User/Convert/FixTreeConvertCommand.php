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

class FixTreeConvertCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert:fix';

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
        ini_set('memory_limit', '-1');
        $this->info(PHP_EOL . 'Binary Trees Started' . PHP_EOL);
        $bar = $this->output->createProgressBar(Tree::count());

        $bar->start();
//        Tree::withoutEvents(function ()  use ($bar){
            Tree::
            withDepth()->
            chunk(200, function ($nodes) use ($bar) {
                foreach ($nodes as $node) {
                    $node->_dpt = $node->depth;

                    if($node->children()->count() == 0){
                        $node->vacancy = VACANCY_ALL;
                    } else if($node->children()->count() == 2) {
                        $node->vacancy = VACANCY_NONE;
                    } else if($node->children()->count() == 1 && $node->hasLeftChild()){
                        $node->vacancy = VACANCY_RIGHT;
                    } else {
                        $node->vacancy = VACANCY_LEFT;
                    }
                    $node->save();

                    if(!is_null($node->parent) && $node->parent->children()->count()>1){
                        $sib = $node->parent->children()->where('id','!=',$node->id)->first();

                        if($node->position == Tree::LEFT){
                            if($node->_lft > $sib->_lft)
                                $node->insertBeforeNode($sib);
                        } else {
                            if($node->_rgt < $sib->_rgt)
                                $node->insertAfterNode($sib);
                        }
                    }

                    $bar->advance();

                }
            });
//        });
        $bar->finish();
        $this->info(PHP_EOL . 'Binary Finished' . PHP_EOL);

        $this->info(PHP_EOL . 'Referral Trees Started' . PHP_EOL);
        $bar = $this->output->createProgressBar(ReferralTree::count());

        $bar->start();
        ReferralTree::withoutEvents(function () use ($bar) {
            ReferralTree::withDepth()->
            chunk(200, function ($nodes) use ($bar) {
                foreach ($nodes as $node) {
                    $node->_dpt = $node->depth;
                    $node->save();
                    $bar->advance();

                }
            });
        });
        $bar->finish();
        $this->info(PHP_EOL . 'Referral Finished' . PHP_EOL);
    }


}
