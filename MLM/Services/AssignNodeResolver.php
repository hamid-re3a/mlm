<?php


namespace MLM\Services;


use Exception;
use Illuminate\Support\Facades\DB;
use MLM\Interfaces\Commission;
use MLM\Models\ReferralTree;
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
     * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|User|User[]
     */
    private $attach_to_user;
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
        $this->attach_to_user = null;
        if ($this->order->getAttachUserId() != 0)
            $this->attach_to_user = $user_service->findByIdOrFail($this->order->getAttachUserId());
        $this->plan = app(AssignNode::class);
    }

    public function handle($simulate = false)
    {
        DB::beginTransaction();
        $msg = trans('responses.error');
        try {
            if ($this->to_user->hasBinaryNode() || $this->to_user->hasReferralNode()) {

                $flag = $this->attachBinary();
                if ($flag == false) {
                    DB::rollBack();
                    return [false, trans('responses.tree-node-not-attached-successful')];
                }


                // add user to referral tree
                $this->to_user->referralTree->appendNode($this->user->buildReferralTreeNode());
                $this->fixNodeDepth();
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

    private function updateNodeVacancy(Tree $node)
    {

        if ($node->children()->count() == 0) {
            $node->vacancy = VACANCY_ALL;
        } else if ($node->children()->count() == 2) {
            $node->vacancy = VACANCY_LEFT;
        } else if ($node->children()->count() == 1 && $node->hasLeftChild()) {
            $node->vacancy = VACANCY_RIGHT;
        } else {
            $node->vacancy = VACANCY_NONE;
        }
        $node->save();

    }


    private function fixLeftAndRightNodes(Tree $node)
    {
        $node->refresh();
        if($node->hasRightChild() || $node->hasLeftChild()){
            if($node->leftChild()->_lft > $node->rightChild()->_lft )
                $node->leftChild()->insertBeforeNode($node->rightChild());
        }

    }
    private function attachUserToBinary(Tree $to_user, string $string)
    {
        $new_tree_node = $this->user->buildBinaryTreeNode();

        if ($string == Tree::LEFT) {

            if (!$to_user->hasLeftChild()) {
                $to_user->appendAsLeftNode($new_tree_node);

                $this->fixLeftAndRightNodes($to_user);
                $this->updateNodeVacancy($to_user);
            } else {
                $lefty = Tree::query()
                    ->where('_lft', '>', $to_user->_lft)
                    ->where('_rgt', '<', $to_user->_rgt)
                    ->where('position', 'left')
                    ->whereIn('vacancy', [VACANCY_ALL, VACANCY_LEFT])
                    ->orderBy('_lft', 'asc')
                    ->limit(1)
                    ->first();
                $lefty->appendAsLeftNode($new_tree_node);
                $this->fixLeftAndRightNodes($lefty);
                $this->updateNodeVacancy($lefty);
            }
        } else

            if (!$to_user->hasRightChild()) {

                $to_user->appendAsRightNode($new_tree_node);
                $this->fixLeftAndRightNodes($to_user);
                $this->updateNodeVacancy($to_user);
            } else {
                $righty = Tree::query()
                    ->where('_lft', '>', $to_user->_lft)
                    ->where('_rgt', '<', $to_user->_rgt)
                    ->whereIn('vacancy', [VACANCY_ALL, VACANCY_RIGHT])
                    ->where('position', 'right')
                    ->orderBy('_rgt', 'desc')
                    ->limit(1)
                    ->first();


                $righty->appendAsRightNode($new_tree_node);
                $this->fixLeftAndRightNodes($righty);
                $this->updateNodeVacancy($righty);
            }

    }

    private function fixNodeDepth(): void
    {
        $user = $this->user;
        $user->refresh();
        Tree::withoutEvents(function () use ($user) {
            $tree = Tree::withDepth()->find($user->binaryTree->id);
            $tree->_dpt = $tree->depth;
            $tree->save();
        });
        ReferralTree::withoutEvents(function () use ($user) {
            $referral = ReferralTree::withDepth()->find($user->referralTree->id);
            $referral->_dpt = $referral->depth;
            $referral->save();
        });;
    }

    /**
     * @return bool
     */
    private function attachBinary(): bool
    {
        $flag = true;
        if (!is_null($this->attach_to_user)) {
            if ($this->attach_to_user->binaryTree->children->count() < 2) {
                if ($this->to_user->hasRole(USER_ROLE_SUPER_ADMIN)) {
                    $new_tree_node = $this->user->buildBinaryTreeNode();
                    if (!$this->attach_to_user->binaryTree->hasRightChild() && $this->order->getAttachUserPosition() == 1 ) {
                        $this->attach_to_user->binaryTree->appendAsRightNode($new_tree_node);

                        $this->fixLeftAndRightNodes($this->attach_to_user->binaryTree);
                        $this->updateNodeVacancy($this->attach_to_user->binaryTree);
                    } else if (!$this->attach_to_user->binaryTree->hasLeftChild() && $this->order->getAttachUserPosition() == 0 ) {
                        $this->attach_to_user->binaryTree->appendAsLeftNode($new_tree_node);

                        $this->fixLeftAndRightNodes($this->attach_to_user->binaryTree);
                        $this->updateNodeVacancy($this->attach_to_user->binaryTree);
                    } else {
                        $flag = false;
                    }

                } else {
                    list($lefty, $righty) = $this->leftyAndRighty($this->to_user->binaryTree);
                    if ($this->attach_to_user->binaryTree->id == optional($lefty)->id && $this->order->getAttachUserPosition() == 0) {
                        if (!$this->attach_to_user->binaryTree->hasLeftChild()) {
                            $new_tree_node = $this->user->buildBinaryTreeNode();
                            $this->attach_to_user->binaryTree->appendAsRightNode($new_tree_node);

                            $this->fixLeftAndRightNodes($this->attach_to_user->binaryTree);
                            $this->updateNodeVacancy($this->attach_to_user->binaryTree);
                        } else {
                            $flag = false;
                        }
                    } else if ($this->attach_to_user->binaryTree->id == optional($righty)->id && $this->order->getAttachUserPosition() == 1) {
                        if (!$this->attach_to_user->binaryTree->hasRightChild()) {
                            $new_tree_node = $this->user->buildBinaryTreeNode();
                            $this->attach_to_user->binaryTree->appendAsLeftNode($new_tree_node);

                            $this->fixLeftAndRightNodes($this->attach_to_user->binaryTree);
                            $this->updateNodeVacancy($this->attach_to_user->binaryTree);
                        } else {
                            $flag = false;
                        }
                    } else {
                        $flag = false;
                    }
                }
            } else {
                $flag = false;
            }


        } else {
            $position = $this->to_user->default_binary_position;
            // add user to binary tree
            $this->attachUserToBinary($this->to_user->binaryTree, $position);
        }
        return $flag;
    }


    private function leftyAndRighty(Tree $tree_node)
    {

        $lefty = $this->getLefty($tree_node);

        $righty = $this->getRighty($tree_node);

        return [$lefty, $righty];
    }


    private function getLefty(Tree $tree_node)
    {
        $lefty = Tree::query()
            ->where('_lft', '>', $tree_node->_lft)
            ->where('_rgt', '<', $tree_node->_rgt)
            ->where('position', 'left')
            ->whereIn('vacancy', [VACANCY_ALL, VACANCY_LEFT])
            ->orderBy('_lft', 'asc')
            ->limit(1)
            ->first();
        return $lefty;
    }


    private function getRighty(Tree $tree_node)
    {
        $righty = Tree::query()
            ->where('_lft', '>', $tree_node->_lft)
            ->where('_rgt', '<', $tree_node->_rgt)
            ->whereIn('vacancy', [VACANCY_ALL, VACANCY_RIGHT])
            ->where('position', 'right')
            ->orderBy('_rgt', 'desc')
            ->limit(1)
            ->first();
        return $righty;
    }
}
