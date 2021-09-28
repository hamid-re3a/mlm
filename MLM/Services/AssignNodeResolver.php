<?php


namespace MLM\Services;


use Exception;
use Illuminate\Support\Facades\DB;
use MLM\Interfaces\Commission;
use MLM\Models\Tree;
use MLM\Services\Plans\AssignNode;
use Orders\Services\Grpc\Order;
use User\Models\User;
use User\Services\UserService;

class AssignNodeResolver
{
    private $order;
    /**
     * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|User|User[]
     */
    private $user;
    /**
     * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|User|User[]
     */
    private $to_user;
    /**
     * @var AssignNode
     */
    private $plan;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $user_service = app(UserService::class);
        $this->user = $user_service->findByIdOrFail($order->getUserId());
        $this->to_user = $user_service->findByIdOrFail($this->user->sponsor_id);
        $this->plan = app(AssignNode::class);
    }

    public function handle($simulate = false)
    {
        DB::beginTransaction();
        $msg = trans('responses.error');
        try {
            if ($this->to_user->hasBinaryNode() || $this->to_user->hasReferralNode()) {
                $position = $this->to_user->default_binary_position;

                // add user to binary tree
                $this->attachUserToBinary($this->to_user->binaryTree, $position);
                // add user to referral tree
                $this->to_user->referralTree->appendNode($this->user->buildReferralTreeNode());

                if ($this->resolve($simulate)) {
                    if (!$simulate)
                        DB::commit();
                    else
                        DB::rollBack();
                    return [true, trans('responses.tree-node-attached-successful')];
                } else {
                    $msg = trans('responses.commission-failed');
                }
            } else {
                $msg = trans('responses.not-valid-sponsor-user-failed');
            }


        } catch (\Throwable $e) {
            DB::rollBack();
            return [false, $e->getMessage()];
        }

        DB::rollBack();
        return [false, $msg];
    }

    public function resolve($simulate = false)
    {
        if ($simulate)
            return true;
        DB::beginTransaction();
        try {
            if ($this->resolveCommission()) {
                DB::commit();
                return true;
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return false;
        }
        DB::rollBack();
        return false;

    }

    public function resolveCommission(): bool
    {
        $isItOk = true;
        DB::beginTransaction();
        try {
            /** @var  $commission Commission */
            foreach ($this->plan->getCommissions() as $commission)
                $isItOk = $isItOk && $commission->calculate($this->order);

            if (!$isItOk) {
                DB::rollBack();
                return false;
            }

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            return false;
        }
        return true;
    }

    private function attachUserToBinary(Tree $to_user, string $string, $call = 1)
    {
        if ($string == Tree::LEFT) {

//            Do not remove this comment

//            $lefty = Tree::query()->whereRaw('_rgt - _lft = 3')->where('position','left')->orderBy('_lft','asc')->descendantsAndSelf(1)->first();
//            $leaf_lefty = Tree::query()->whereRaw('_rgt - _lft = 1')->where('position','left')->orderBy('_lft','asc')->descendantsAndSelf(1)->first();
//            if(is_null($lefty) || $lefty->_lft >= $leaf_lefty->_lft || $lefty->hasLeftChild()){
//                $nominee = $leaf_lefty;
//            } else {
//                $nominee = $lefty;
//            }

            if (!$to_user->hasLeftChild()) {
                return $to_user->appendAsLeftNode($this->user->buildBinaryTreeNode());
            } else {
                return $this->attachUserToBinary($to_user->children()->left()->first(), $string, ++$call);
            }
        } else
            if (!$to_user->hasRightChild()) {
                return $to_user->appendAsRightNode($this->user->buildBinaryTreeNode());
            } else {
                return $this->attachUserToBinary($to_user->children()->right()->first(), $string,++$call);
            }

    }
}
