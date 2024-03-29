<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use MLM\Models\ReferralTree;
use MLM\Models\Tree;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('roi:trading')->dailyAt('00:00');
        $schedule->command('roi:residual')->dailyAt('12:00');
        $schedule->command('queue:retry all')->everyThreeHours();
//        $schedule->call(function(){
//            Tree::withoutEvents(function(){Tree::fixTree();});
//            ReferralTree::withoutEvents(function(){ReferralTree::fixTree();});
//        })->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
