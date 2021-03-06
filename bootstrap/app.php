<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    (new Dotenv\Dotenv(__DIR__ . '/../'))->load();
} catch (Dotenv\Exception\InvalidPathException $e) {
    //
}

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
 */

$app = new ElemenX\AdvancedRoute\Application(
    realpath(__DIR__ . '/../')
);

$app->withFacades();
$app->withEloquent();

foreach (glob(config_path('*.php')) as $file) {
    $app->configure(basename($file, '.php'));
}

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
 */

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Essential\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Essential\Console\Kernel::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
 */

$app->middleware([
    Nord\Lumen\Cors\CorsMiddleware::class,
    Fideloper\Proxy\TrustProxies::class
]);

$app->routeMiddleware([
    'jwt' => App\Essential\Middleware\RefreshToken::class
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
 */

//3rd party Packages
$app->register(Illuminate\Redis\RedisServiceProvider::class);
$app->register(Illuminate\Filesystem\FilesystemServiceProvider::class);
$app->register(Nord\Lumen\Cors\CorsServiceProvider::class);
$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);
$app->register(Intervention\Image\ImageServiceProvider::class);
$app->register(Sentry\SentryLaravel\SentryLumenServiceProvider::class);
$app->register(ElemenX\ApiPagination\PaginationServiceProvider::class);
$app->register(Freyo\Flysystem\QcloudCOSv5\ServiceProvider::class);

//local Packages
$app->register(App\Essential\Providers\AppServiceProvider::class);
$app->register(App\Essential\Providers\DatabaseServiceProvider::class);
$app->register(App\Essential\Providers\ValidationServiceProvider::class);

$app->singleton(Illuminate\Auth\AuthManager::class, function ($app) {
    return $app->make('auth');
});

$app->singleton(
    Illuminate\Contracts\Filesystem\Factory::class,
    function ($app) {
        return new Illuminate\Filesystem\FilesystemManager($app);
    }
);

$app->bind('captcha', function ($app) {
    return new App\Essential\Services\CaptchaService(
        $app['Illuminate\Config\Repository'],
        $app['Intervention\Image\ImageManager'],
        $app['Illuminate\Hashing\BcryptHasher'],
        $app['Illuminate\Support\Str']
    );
});

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
 */

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__ . '/../routes/api.php';
});

return $app;
