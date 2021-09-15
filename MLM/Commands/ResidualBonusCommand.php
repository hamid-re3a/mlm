<?php

namespace MLM\Commands;

use Illuminate\Console\Command;
use MLM\Jobs\ResidualBonusCommissionJob;
use MLM\Jobs\TradingProfitCommissionJob;
use MLM\Models\OrderedPackage;
use MLM\Models\PackageRoi;
use MLM\Models\ResidualBonusSetting;
use User\Models\User;

class ResidualBonusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roi:residual';

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


        $min_rank =ResidualBonusSetting::query()->min('rank');

        $users = User::query()->where('rank','>=',$min_rank)->get();



        $bar = $this->output->createProgressBar($users->count());
        $this->info(PHP_EOL . 'Start residual bonus commissions');
        $bar->start();

        foreach ($users as $item) {
            ResidualBonusCommissionJob::dispatch($item);
            $bar->advance();
        }


        $bar->finish();
        $this->info(PHP_EOL . 'Residual bonus commissions completed successfully' . PHP_EOL);

    }


}
