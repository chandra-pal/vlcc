<?php
 namespace Modules\Admin\Tests;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample()
    {
        // Logged In with following 2 lines
        $user = new \Modules\Admin\Models\User(array('username' => 'gaurav'));
        $this->be($user);

        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\DashboardController@index');
        $this->assertResponseStatus($response->status());
    }
}
