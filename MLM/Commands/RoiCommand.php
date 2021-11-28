<?php

namespace MLM\Commands;

use Illuminate\Console\Command;
use MLM\Jobs\TradingProfitCommissionJob;
use MLM\Models\OrderedPackage;
use MLM\Models\PackageRoi;

class RoiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roi:trading';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily Roi Command';

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


        if(!PackageRoi::query()->today()->exists()){
            $this->info(PHP_EOL . 'Admin has not set any ROI for today.' . PHP_EOL);
            return;
        }

        $ordered_packages_count = OrderedPackage::query()->active()->notSpecial()->canGetRoi()->count();
        $bar = $this->output->createProgressBar($ordered_packages_count);
        $this->info(PHP_EOL . 'Start trading profits');
        $bar->start();
        OrderedPackage::
        query()->active()->notSpecial()->canGetRoi()->
        chunk(100, function ($ordered_packages) use ($bar) {
            foreach ($ordered_packages as $item) {
                TradingProfitCommissionJob::dispatch($item);
                $bar->advance();
            }
        });


        $bar->finish();
        $this->info(PHP_EOL . 'Trading profits completed successfully' . PHP_EOL);


    }


}
