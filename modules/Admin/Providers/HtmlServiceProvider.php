<?php
/**
 * To Html service provider for Admin module
 * Along with Form and HTML builder with CSRF token handling
 *
 * @author Gaurav Patel <gauravp@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Providers;

use Collective\Html\HtmlServiceProvider as ServiceProvider;
use Modules\Admin\Services\Html\FormBuilder;
use Modules\Admin\Services\Html\HtmlBuilder;

class HtmlServiceProvider extends ServiceProvider
{

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerFormBuilder();
        $this->registerHtmlBuilder();
    }

    /**
     * Register the form builder instance.
     *
     * @return form with storing csrf token in session
     */
    protected function registerFormBuilder()
    {
 
        $this->app->singleton('form', function($app) {
            $form = new FormBuilder($app['html'], $app['url'], $app['session.store']->getToken());

            return $form->setSessionStore($app['session.store']);
        });
    }

    /**
     * Register the html builder instance.
     *
     * @return Modules\Admin\Services\Html\HtmlBuilder instance
     */
    protected function registerHtmlBuilder()
    {
        $this->app->singleton('html', function($app) {
            return new HtmlBuilder($app['url']);
        });
    }
}
