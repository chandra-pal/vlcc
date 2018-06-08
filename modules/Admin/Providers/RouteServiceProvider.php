<?php
/**
 * To Route provider for Admin module
 * Along with model binding and namespace and group prefix
 *
 * @author Gaurav Patel <gauravp@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Providers;

use Illuminate\Routing\Router,
    Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{

    /**
     * This namespace is applied to the controller routes in your routes file.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'Modules\Admin\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot(Router $router)
    {
        parent::boot($router);
    }

    /**
     * Define the routes for the application.
     *
     * @param \Illuminate\Routing\Router $router
     * @return void
     */
    public function map(Router $router)
    {
        /*
        $router->group(['prefix' => 'admin', 'namespace' => $this->namespace], function($router) {

            $modulePath = \Pingpong\Modules\Facades\Module::getModulePath('admin');
            $moduleRoutePath = $modulePath . 'Http/routes.php';
            require $moduleRoutePath;

            $router->controllers([
                'auth' => 'Auth\AuthController',
                'password' => 'Auth\PasswordController',
            ]);
        });
         * *
         */
    }
}
