<?php

namespace User;

use App\Jobs\User\UserGetDataJob;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use User\Models\User;
use User\Services\UserService;
use User\Services\UserUpdate;

class UserServiceProvider extends ServiceProvider
{
    private $routes_namespace = 'User\Http\Controllers';
    private $namespace = 'User';
    private $name = 'User';
    private $config_file_name = 'user';

    /**
     * Register API class.
     *
     * @return void
     */
    public function register()
    {
        if (!$this->app->runningInConsole()) {
            return;
        }
        if ($this->shouldMigrate()) {
            $this->loadMigrationsFrom([
                __DIR__ . '/database/migrations',
            ]);
        }
        $this->publishes([
            __DIR__ . '/database/migrations' => database_path('migrations'),
        ], $this->name . '-migrations');

    }

    /**
     * Bootstrap API resources.
     *
     * @return void
     */
    public function boot()
    {
        Auth::viaRequest('r2f-sub-service', function (Request $request) {

            if (!function_exists('updateUserFromGrpcServer')) {
                /**
                 * @param Request $request
                 * @return array
                 */
                function updateUserFromGrpcServer(Request $request): ?\User\Services\User
                {
                    $client = new \User\Services\UserServiceClient('staging-api-gateway.janex.org:9595', [
                        'credentials' => \Grpc\ChannelCredentials::createInsecure()
                    ]);
                    $id = new \User\Services\Id();
                    $id->setId((int)$request->header('X-user-id'));
                    try {
                        /** @var $user \User\Services\User */
                        list($user, $status) = $client->getUserById($id)->wait();
                        if ($status->code == 0) {
                            app(UserService::class)->userUpdate($user);
                            return $user;
                        }
                        return null;
                    } catch (\Exception $exception) {
                        return null;
                    }
                }
            }

            if (
                $request->hasHeader('X-user-id')
                && $request->hasHeader('X-user-hash')
                && is_numeric($request->header('X-user-id'))
            ) {


                $user_update = new UserUpdate();
                $user_update->setId($request->header('X-user-id'));
                $user_update->setQueueName('subscriptions');


                $user_hash_request = $request->header('X-user-hash');
                $user = User::query()->find($request->header('X-user-id'));

                /**
                 * if there is not exist user. get data user complete from api gateway
                 * error code 470 is for data user not exist log for development
                 */
                if ($user === null) {
                    $service_user = updateUserFromGrpcServer($request);
                    if ($service_user === null)
                        throw new Exception('please try another time!', 470);
                    $user->refresh();
                }

                $hash_user_service = md5(serialize($user->getUserService()));

                /**
                 * if there is not update data user. get data user complete from api gateway
                 * error code 471 is for data user not update log for development
                 */
                if ($hash_user_service != $user_hash_request) {
                    $service_user = updateUserFromGrpcServer($request);
                    $hash_user_service = md5(serialize($service_user));
                    if ($hash_user_service != $user_hash_request) {
                        UserGetDataJob::dispatch($user_update);
                        throw new Exception('please try another time!', 471);
                    }
                }


                $request->merge([
                    'user' => $user
                ]);
                return $user;
            }

        });


        $this->setupConfig();

        $this->registerHelpers();

        Route::prefix('v1/user')
            ->middleware('api')
            ->namespace($this->routes_namespace)
            ->group(__DIR__ . '/routes/api.php');

        if ($this->app->runningInConsole()) {
            $this->seed();

            $this->publishes([
                __DIR__ . '/config/' . $this->config_file_name . '.php' => config_path($this->config_file_name . '.php'),
            ], 'api-response');
        }
    }

    /**
     * Set Config files.
     */
    protected function setupConfig()
    {
        $path = realpath($raw = __DIR__ . '/config/' . $this->config_file_name . '.php') ?: $raw;
        $this->mergeConfigFrom($path, 'api');
    }


    /**
     * Register helpers.
     */
    protected function registerHelpers()
    {
        if (file_exists($helperFile = __DIR__ . '/helpers/helpers.php')) {
            require_once $helperFile;
        }
    }


    /**
     * Determine if we should register the migrations.
     *
     * @return bool
     */
    protected function shouldMigrate()
    {
        return UserConfigure::$runsMigrations;
    }

    private function seed()
    {
        if (isset($_SERVER['argv']))
            if (array_search('db:seed', $_SERVER['argv'])) {
                UserConfigure::seed();
            }
    }

}
