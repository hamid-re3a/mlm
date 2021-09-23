<?php

use Illuminate\Support\Facades\DB;
use MLM\Models\EmailContentSetting;
use User\Models\User;
use Wallets\Services\Grpc\Deposit;

if (!function_exists('subset')) {


    function subset(Illuminate\Support\Collection $collection, $set_number = 1, $chunk_size = 3, $from_node = 0, $sortBy = 'id')
    {
        return $collection->sortBy($sortBy)
            ->values()->filter(function ($item, $index) use ($set_number, $chunk_size, $from_node) {
                $start_index = $index - $from_node;
                return floor($start_index / $chunk_size) + 1 == $set_number;
            })->values()->toArray();
    }
}

if (!function_exists('getRank')) {

    function getRank($rank)
    {
        if (Illuminate\Support\Facades\DB::table('ranks')->exists()) {
            $key_db = \MLM\Models\Rank::query()->where('rank', $rank)->first();
            if ($key_db && !empty($key_db->value))
                return $key_db->toArray();
        }

        if (isset(RANKS[$rank]))
            return RANKS[$rank];

        return null;
    }
}

if (!function_exists('payCommission')) {
    /**
     * @param Deposit $deposit_service_object
     * @param User $user
     * @param $type
     * @param null $package_id
     * @throws Exception
     */
    function payCommission(Deposit $deposit_service_object, User $user, $type, $package_id = null): void
    {
        DB::beginTransaction();
        try {
            $commission = $user->commissions()->create([
                'amount' => $deposit_service_object->getAmount(),
                'ordered_package_id' => $package_id,
                'type' => $type,
            ]);

            if (app()->environment() != 'testing') {
                if ($commission) {
                    $deposit_service_object->setPayloadId($commission->id);
                    /** @var $deposit_response  Deposit */
                    list($deposit_response, $error) = getWalletGrpcClient()->deposit($deposit_service_object)->wait();
                    if ($error != 0) {
                        throw new \Exception('Wallet Service Error');
                    }

                    $commission->transaction_id = $deposit_response->getTransactionId();
                    $commission->save();
                } else {
                    throw new \Exception('Commission Failed Error');
                }
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception('Commission Error => ' . $exception->getMessage());
        }

        DB::commit();

    }
}
if (!function_exists('getPackageGrpcClient')) {
    function getPackageGrpcClient()
    {
        return new \Packages\Services\Grpc\PackagesServiceClient('staging-api-gateway.janex.org:9596', [
            'credentials' => \Grpc\ChannelCredentials::createInsecure()
        ]);
    }
}
if (!function_exists('getWalletGrpcClient')) {
    function getWalletGrpcClient()
    {
        return new \Wallets\Services\Grpc\WalletServiceClient('staging-api-gateway.janex.org:9596', [
            'credentials' => \Grpc\ChannelCredentials::createInsecure()
        ]);
    }
}


if (!function_exists('userRankBasedOnConvertedPoint')) {
    function userRankBasedOnConvertedPoint($converted_point): \MLM\Models\Rank
    {
        return \MLM\Models\Rank::query()->where('condition_converted_in_bp', '<=', $converted_point / BF_TO_BB_RATIO)->orderBy('rank', 'desc')->first();
    }
}
if (!function_exists('getAndUpdateUserRank')) {

    function getAndUpdateUserRank(\User\Models\User $user): ?\MLM\Models\Rank
    {
        $ranks = \MLM\Models\Rank::query()->orderBy('rank', 'desc')->get();
        foreach ($ranks as $rank) {
            if ($user->binaryTree->converted_points >= BF_TO_BB_RATIO * $rank->condition_converted_in_bp) {


                $left_binary_children = $user->binaryTree->leftSideChildrenIds();
                $right_binary_children = $user->binaryTree->rightSideChildrenIds();
                if ($rank->condition_direct_or_indirect) {
                    $referral_children = $user->referralTree->descendantsUserIds();
                } else {
                    $referral_children = $user->referralTree->childrenUserIds();
                }

                $left_binary_sponsored_children = array_intersect($left_binary_children, $referral_children);
                $right_binary_sponsored_children = array_intersect($right_binary_children, $referral_children);

                if (User::hasLeastChildrenWithRank($left_binary_sponsored_children, $rank->condition_sub_rank, $rank->condition_number_of_left_children) &&
                    User::hasLeastChildrenWithRank($right_binary_sponsored_children, $rank->condition_sub_rank, $rank->condition_number_of_right_children)) {
                    $user->rank = $rank->rank;
                    $user->save();
                    return $rank;
                }
            }
        }

        return null;

    }
}


if (!function_exists('getMLMSetting')) {

    function getSetting($key)
    {
        //Check if settings are available in cache
        if (cache()->has('mlm_settings'))
            if ($setting = collect(cache('mlm_settings'))->where('name', $key)->first())
                return $setting['value'];

        $setting = \MLM\Models\Setting::query()->where('name', $key)->first();
        if ($setting)
            return $setting->value;


        if (defined('MLM_SETTINGS') AND is_array(MLM_SETTINGS) AND array_key_exists($key, MLM_SETTINGS))
            return MLM_SETTINGS[$key]['value'];

        \Illuminate\Support\Facades\Log::error('mlmSetting => ' . $key);
        throw new Exception(trans('mlm.responses.settings.key-doesnt-exists', ['key' => $key]));
    }
}


function getEmailAndTextSetting($key)
{
    // Comment Test
    if (DB::table('email_content_settings')->exists()) {
        $setting = EmailContentSetting::query()->where('key', $key)->first();
        if ($setting && !empty($setting->body))
            return $setting->toArray();
    }

    if (isset(EMAIL_CONTENT_SETTINGS[$key]))
        return EMAIL_CONTENT_SETTINGS[$key];

    throw new Exception(trans('user.responses.main-key-settings-is-missing'));
}
