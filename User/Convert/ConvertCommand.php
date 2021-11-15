<?php

namespace User\Convert;

use Carbon\Carbon;
use Illuminate\Console\Command;
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
                if (!is_null($item->detail)
                    && !is_null($item->detail->user_detail_email)
                    && !empty($item->detail->user_detail_email) &&
                    filter_var($item->detail->user_detail_email, FILTER_VALIDATE_EMAIL)
                ) {
                    if (User::query()->where('email', $item->detail->user_detail_email)->exists())
                        $email = $item->user_name . '@dreamcometrue.ai';
                    else
                        $email = $item->detail->user_detail_email;
                } else {
                    $email = $item->user_name . '@dreamcometrue.ai';
                }

                $current_user->update([
                    'email' => $email,
                    'first_name' => (!is_null($item->detail) && !is_null($item->detail->user_detail_name)) ? $item->detail->user_detail_name : "Unknown",
                    'last_name' => (!is_null($item->detail) && !is_null($item->detail->user_detail_second_name)) ? $item->detail->user_detail_second_name : "Unknown",
                    'gender' => (!is_null($item->detail) && !is_null($item->detail->user_detail_gender)) ? ($item->detail->user_detail_gender == "F") ? "Female" : "Male" : "Male",
                    'sponsor_id' => $item->sponsor_id,
                    'username' => $item->user_name,
                ]);
                $current_user->assignRole(USER_ROLE_CLIENT);

                if ($item->sponsor_id) {
                    $sponsor = User::query()->firstOrCreate(['id' => $item->sponsor_id]);
                    $sponsor_tree = $sponsor->buildReferralTreeNode();
                    $sponsor_tree->appendNode($current_user->buildReferralTreeNode());
                }
                if ($item->father_id) {
                    $parent = User::query()->firstOrCreate(['id' => $item->father_id]);
                    if(is_null($parent->binaryTree))
                        $parent->binaryTree()->create();
                    if(is_null($current_user->binaryTree))
                        $current_user->binaryTree()->create();

                    $parent->refresh();
                    $current_user->refresh();

                    if ($item->position == "L")
                        $parent->binaryTree->appendAsLeftNode($current_user->binaryTree);
                    else
                        $parent->binaryTree->appendAsRightNode($current_user->binaryTree);
                }

                $bar->advance();
            }


        });

        $bar->finish();
        $this->info(PHP_EOL . 'User Conversion Finished' . PHP_EOL);
    }


}