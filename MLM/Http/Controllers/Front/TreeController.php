<?php

namespace MLM\Http\Controllers\Front;


use MLM\Http\Requests\ReferralTreeRequest;
use MLM\Http\Resources\Tree\ReferralTreeResource;
use MLM\Models\ReferralTree;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class TreeController extends Controller
{
    use  ValidatesRequests;

    /**
     * Get Referral Tree
     * @group
     * Public User > Referral Tree
     */
    public function getUserReferralTree(ReferralTreeRequest $request)
    {

        if(auth()->check() && !auth()->user()->isNetworker())
            return api()->error();

        if ($request->has('id') && request('id'))
            $tree = ReferralTree::find(request('id'));
        else
            $tree = ReferralTree::where('user_id',auth()->id())->first();
        $page = 1;
        if ($request->has('page') && request('page'))
            $page = request('page');



        $data = [
            'children' => ReferralTreeResource::collection($tree->children()->paginate(25)),
            'id' => $tree->id,
            'hasOrder' => $tree->user->orders()->count() > 0,
            'hasChildren' => $tree->children()->exists(),
            'hasMore'=>$tree->children()->count() > $page*25,
            'childrenCount' => $tree->children()->count(),
            'page' => $page
        ];
        return api()->success('', $data);
    }

}
