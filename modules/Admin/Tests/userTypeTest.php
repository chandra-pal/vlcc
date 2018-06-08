<?php
/**
 * Test Case For UserTypeController
 *
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Tests;

class UserTypeTest extends TestCase
{

    /**
     * index action of UserTypeController test case
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\UserTypeController@index');
        $this->assertResponseStatus($response->status());
        //$this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
    }

    /**
     * getData action of UserTypeController test case
     *
     * @return void
     */
    public function getData()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\UserTypeController@getData');
        $this->assertResponseStatus($response->status());
    }

    /**
     * create action of UserTypeController test case
     *
     * @return void
     */
    public function testCreate()
    {
        $response = $this->action('POST', '\Modules\Admin\Http\Controllers\UserTypeController@create');
        $this->assertResponseStatus($response->status());
    }

    /**
     * store action of UserTypeController test case
     *
     * @return void
     */
    public function testStore()
    {
        $response = $this->action('POST', '\Modules\Admin\Http\Controllers\UserTypeController@store');
        $this->assertResponseStatus($response->status());
    }

    /**
     * edit action of UserTypeController test case
     *
     * @return void
     */
    public function testEdit()
    {
        $response = $this->action('POST', '\Modules\Admin\Http\Controllers\UserTypeController@edit');
        $this->assertResponseStatus($response->status());
    }

    /**
     * update action of UserTypeController test case
     *
     * @return void
     */
    public function testUpdate()
    {
        $response = $this->action('POST', '\Modules\Admin\Http\Controllers\UserTypeController@update');
        $this->assertResponseStatus($response->status());
    }
}
