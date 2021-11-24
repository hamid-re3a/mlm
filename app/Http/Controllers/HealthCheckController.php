<?php

namespace App\Http\Controllers;

class HealthCheckController extends Controller
{
    public function _healthz()
    {
        try {

            //Check DB connection
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            \Illuminate\Support\Facades\DB::connection()->getDatabaseName();

            return api()->success(null,[
                'subject' => 'What do you want to see here ?'
            ]);
        } catch (\Throwable $exception) {
            return api()->error(null,[
                'subject' => $exception->getMessage()
            ]);
        }
    }

}
