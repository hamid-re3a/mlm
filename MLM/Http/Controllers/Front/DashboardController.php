<?php

namespace MLM\Http\Controllers\Front;


use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use MLM\Http\Requests\Dashboard\DashboardRequest;
use MLM\Models\Tree;
use User\Models\User;

class DashboardController extends Controller
{
    use  ValidatesRequests;

    /**
     * Binary member chart
     * @group
     * Public User > User MLM Dashboard
     *
     */
    public function binaryMembers(DashboardRequest $request)
    {
        if (auth()->check() && !auth()->user()->hasBinaryNode())
            return api()->error();
        /** @var  $user User*/
        $user = auth()->user();
        $function_left_members = function ($from_day, $to_day) use ($user){
            if(!$user->binaryTree->hasLeftChild()){
                return null;
            }
            return Tree::query()->whereBetween('created_at',[$from_day,$to_day])->descendantsAndSelf($user->binaryTree->leftChild()->id);
        };
        $function_right_members = function ($from_day, $to_day) use ($user){
            if(!$user->binaryTree->hasRightChild()){
                return null;
            }
            return Tree::query()->whereBetween('created_at',[$from_day,$to_day])->descendantsAndSelf($user->binaryTree->rightChild()->id);
        };
        $sub_function = function ($collection, $intervals) {
            if(is_null($collection))
                return 0;
            return $collection->whereBetween('created_at', $intervals)->count();
        };

        $final_result = [];
        $final_result['left'] = chartMaker($request->type, $function_left_members, $sub_function);
        $final_result['right'] = chartMaker($request->type, $function_right_members, $sub_function);
        return api()->success('',$final_result );
    }
}
