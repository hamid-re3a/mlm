<?php

namespace User\Commands;

use Illuminate\Console\Command;
use User\Models\User;

class UpdateUsersFromGrpc extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:update-grpc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all users';

    /**
     * Execute the console command.
     *
     * @return void
     * @throws \Exception
     */
    public function handle()
    {
        $bar = $this->output->createProgressBar(User::query()->count());
        $this->info(PHP_EOL . 'Start update all users' . PHP_EOL);
        $bar->start();
        User::query()->chunkById(100, function($users) use($bar){
            foreach($users as $user) {
                updateUserFromGrpcServer($user->id);
                $bar->advance();
            }
        });
        $bar->finish();
        $this->info(PHP_EOL . 'Users updated' . PHP_EOL);
    }

}
