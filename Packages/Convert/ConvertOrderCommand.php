<?php

namespace Packages\Convert;

use Illuminate\Console\Command;
use MLM\Repository\OrderedPackageRepository;
use Orders\Services\Grpc\Order;
use Orders\Services\Grpc\OrderPlans;
use Packages\Convert\Models\UserPackageInfo;
use Packages\Models\Package;
use Packages\Services\Grpc\Id;
use Packages\Services\PackageService;
use User\Models\User;

class ConvertOrderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert:packages';

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
        $sorted_packages = Package::query()->orderBy("price", 'desc')->get();
        $biggest_package = Package::query()->orderBy("price", 'asc')->first();

        $count = UserPackageInfo::query()->count();
        $this->info(PHP_EOL . 'number of user rows ' . $count . PHP_EOL);

        $bar = $this->output->createProgressBar($count);

        $this->info(PHP_EOL . 'Start user conversion');
        $bar->start();
        $users = UserPackageInfo::with('lastPackage')->
        chunk(50, function ($users) use ($bar, $sorted_packages,$biggest_package) {

            foreach ($users as $item) {
                $current_user = User::query()->find($item->user_id);
                if (!$current_user)
                    $current_user = User::factory()->create(['id' => $item->user_id]);
                if (!is_null($item->lastPackage)) {
                    $plan = $this->selectPlan((int)$item->lastPackage->product_value, (int)$item->total_debit);
                    if(!is_null($plan)){
                        /** @var  $new_package Package */
                        $new_package = $this->mapOldPackageToNew($item->lastPackage->product_value, $sorted_packages,$biggest_package);
                        $id = new Id();
                        $id->setId((int)$new_package->id);
                        $package_grpc = app(PackageService::class)->packageFullById($id);
                        $order = new Order();
                        $order->setId((int)$item->user_id);
                        $order->setUserId((int)$item->user_id);
                        $order->setPlan($plan);
                        $order->setIsPaidAt(now()->toDateString());
                        $order->setIsResolvedAt(now()->toDateString());
                        $order->setIsCommissionResolvedAt(now()->toDateString());
                        $order->setCreatedAt(now()->toDateString());
                        $order->setUpdatedAt(now()->toDateString());
                        $order->setValidityInDays((int)200);
                        (new OrderedPackageRepository)->updateOrderAndPackage($order, $package_grpc);
                    }
                }

                $bar->advance();
            }


        });

        $bar->finish();
        $this->info(PHP_EOL . 'User Conversion Finished' . PHP_EOL);
    }


    public function mapOldPackageToNew($price, $packages,$biggest_package)
    {

        $package = $packages->where('price', '>=', (int)$price)->first();
        if(!$package)
            return $biggest_package;
        return $package;
    }
    private function selectPlan(int $package_amount, int $withdrawal_amount)
    {

        if ($withdrawal_amount <= $package_amount * 5 / 100) {
            return OrderPlans::ORDER_PLAN_START;
        } else if ($withdrawal_amount <= $package_amount * 50 / 100) {
            return OrderPlans::ORDER_PLAN_START_50;
        } else if ($withdrawal_amount <= $package_amount * 75 / 100) {
            return OrderPlans::ORDER_PLAN_START_75;
        }
        return null;
    }


}
