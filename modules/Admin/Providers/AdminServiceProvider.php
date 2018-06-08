<?php
/**
 * To Admin service provider for Admin module
 * Handling mapping and register of Config/Views/Transaltion/Midlleware
 *
 * @author Gaurav Patel <gauravp@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Providers;

use Modules\Admin\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Validator;
use Modules\Admin\Services\Validation;

class AdminServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->registerConfig();
        $this->registerTranslations();
        $this->registerValidators();
        $this->registerViews();

        $this->registerMiddleware($router);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Register Middleware.
     *
     * @return void
     */
    protected function registerMiddleware($router)
    {
        $router->middleware('authAdmin', \Modules\Admin\Http\Middleware\Authenticate::class);
        $router->middleware('ajax', \Modules\Admin\Http\Middleware\IsAjax::class);
        $router->middleware('admin', \Modules\Admin\Http\Middleware\IsAdmin::class);
        $router->middleware('acl', \Modules\Admin\Http\Middleware\CheckPermission::class);
        $router->middleware('guestAdmin', \Modules\Admin\Http\Middleware\RedirectIfAuthenticated::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('admin.php'),
        ]);
        $this->mergeConfigFrom(
            __DIR__ . '/../Config/config.php', 'admin'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = base_path('views/modules/admin');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom([$viewPath, $sourcePath], 'admin');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = base_path('resources/lang/modules/admin');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'admin');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'admin');
        }
    }

    /**
     * Register Validators.
     *
     * @return Modules\Admin\Services\Validation instance
     */
    public function registerValidators()
    {
        Validator::resolver(function($translator, $data, $rules, $messages) {
            return new Validation($translator, $data, $rules, $messages);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }
}
