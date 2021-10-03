<?php


namespace MLM\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use MLM\Http\Requests\Admin\UpdateSettingRequest;
use MLM\Http\Resources\Admin\SettingResource;
use MLM\Models\Setting;

class SettingController extends Controller
{

    /**
     * Get settings list
     * @group Admin User > Settings
     */
    public function index()
    {
        $settings = Setting::all();
        return api()->success(null,SettingResource::collection($settings));
    }

    /**
     * Update setting
     * @group Admin User > Settings
     * @param UpdateSettingRequest $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function update(UpdateSettingRequest $request)
    {
        $setting = Setting::query()->where('name',$request->get('name'))->first();
        $setting->update([
            'value' => $request->get('value'),
            'title' => $request->has('title') ? $request->get('title') : $setting->title,
            'description' => $request->has('description') ? $request->get('description') : $setting->description
        ]);
        $settings = Setting::all()->toArray();
        cache(['mlm_settings' =>  $settings]);

        return api()->success(null, SettingResource::make($setting->fresh()));
    }
}
