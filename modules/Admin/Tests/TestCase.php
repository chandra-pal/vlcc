<?php namespace Modules\Admin\Tests;

class TestCase extends \Illuminate\Foundation\Testing\TestCase
{

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost/iplaravel/public/index.php';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../../../bootstrap/app.php';
        $app->register('Modules\Admin\Providers\AdminServiceProvider');
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        return $app;
    }

    /**
     * Setup DB before each test.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        //$this->app['config']->set('database.default', 'sqlite');
        //$this->app['config']->set('database.connections.sqlite.database', ':memory:');

        //$this->migrate();
    }
}
