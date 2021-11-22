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


}
