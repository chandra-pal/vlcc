<?php
/**
 * Test Case For LoginLogsController
 *
 *
 * @author Anagha Athale <anaghaa@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Tests;

use Illuminate\Foundation\Testing\WithoutMiddleware,
    Auth;

class Loginlogs extends TestCase
{

    use WithoutMiddleware;

    /**
     * Index action of LinkCategory Controller test case
     *
     * @return void
     */
    public function testIndex()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\LoginLogsController@index');
        $this->assertResponseStatus($response->status());
        $this->assertResponseOk();
    }

    /**
     * GetData action of LinkCategory Controller test case
     *
     * @return void
     */
    public function testGetData()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\LoginLogsController@getData');
        $this->assertResponseStatus($response->status());
        $this->assertResponseOk();
    }

    /**
     * Group action of LinkCategory Controller test case
     *
     * @return void
     */
    public function testGroupAction()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\LoginLogsController@groupAction');
        $this->assertResponseStatus($response->status());
    }
}
