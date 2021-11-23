<?php


namespace MLM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use MLM\Http\Requests\Admin\UpdateEmailContentRequest;
use MLM\Http\Resources\Admin\EmailContentResource;
use MLM\Models\EmailContentSetting;

class EmailContentController extends Controller
{

    /**
     * Get emails list
     * @group Admin User > Settings > EmailContents
     */
    public function index()
    {
        $emails = EmailContentSetting::all();
        return api()->success(null,EmailContentResource::collection($emails));
    }

    /**
     * Update emails
     * @group Admin User > Settings > EmailContents
     * @param UpdateEmailContentRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(UpdateEmailContentRequest $request)
    {
        $email = EmailContentSetting::query()->where('key',$request->get('key'))->first();
        $email->update($request->except(['key','id','type']));
        $emails = EmailContentSetting::all()->toArray();
        cache(['wallet_email_contents' =>  $emails]);

        return api()->success(null, EmailContentResource::make($email->refresh()));
    }
}
