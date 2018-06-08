<?php
/**
 * Test Case For IpAddressController
 *
 *
 * @author Anagha Athale <anaghaa@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Tests;

use Illuminate\Foundation\Testing\WithoutMiddleware,
    Auth;

class Ipadress extends TestCase
{

    use WithoutMiddleware;

    /**
     * Index action of State Controller test case
     *
     * @return void
     */
    public function testIndex()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\IpAddressController@index');
        $this->assertResponseStatus($response->status());
        $this->assertResponseOk();
    }

    /**
     * getData action of State Controller test case
     *
     * @return void
     */
    public function testGetData()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\IpAddressController@getData');
        $this->assertResponseStatus($response->status());
        $this->assertResponseOk();
    }
    
    /**
     * create action of State Controller test case
     *
     * @return void
     */
    public function testCreate()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\IpAddressController@create');
        $this->assertResponseStatus($response->status());
        $this->assertResponseOk();
    }
    
    /**
     * store action of State Controller test case
     *
     * @return void
     */
    public function testStore()
    {
        Auth::loginUsingId(1);
        $response = $this->action('POST', '\Modules\Admin\Http\Controllers\IpAddressController@store',['ip_address'=>'115.119.100.58','status'=>1,'created_by'=>1,'created_at'=>date('Y-m-d h:i:s'),'updated_by'=>0,'updated_at'=>'']);
        $this->assertResponseStatus($response->status());
        $this->assertResponseOk();
    }
    
    /**
     * edit action of State Controller test case
     *
     * @return void
     */
    public function testEdit()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\IpAddressController@edit', ['id' => 2]);
        $this->assertResponseStatus(200, $response->status());
        $this->assertResponseOk();
    }

    /**
     * update action of State Controller test case
     *
     * @return void
     */
    public function testUpdate()
    {
        Auth::loginUsingId(1);
        $response = $this->action('PUT', '\Modules\Admin\Http\Controllers\IpAddressController@update', ['id'=>5,'ip_address'=>'115.119.100.58','status'=>1,'created_by'=>0,'created_at'=>'','updated_by'=>1,'updated_at'=>date('Y-m-d h:i:s')]);
        $this->assertResponseStatus($response->status());
    }

    /**
     * groupAction action of State Controller test case
     *
     * @return void
     */
    public function testGroupAction()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\IpAddressController@getData');
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);

        $actual = $response->getContent();
        $responseJsonData = json_decode($actual, true);

        $this->assertArrayHasKey('draw', $responseJsonData);
        $this->assertArrayHasKey('recordsTotal', $responseJsonData);
        $this->assertArrayHasKey('recordsFiltered', $responseJsonData);
        $this->assertArrayHasKey('data', $responseJsonData);
        $this->assertArrayHasKey('input', $responseJsonData);

        if (!empty($responseJsonData['data'])) {
            $keys = ['id', 'ip_address', 'status', 'created_by', 'action'];

            foreach ($keys as $key) {
                $this->assertArrayHasKey($key, $responseJsonData['data'][0]);
            }
        }
    }
    
}
