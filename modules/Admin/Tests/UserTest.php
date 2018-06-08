<?php
/**
 * Test Case For User Controller
 *
 *
 * @author Anagha Athale <anaghaa@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Tests;

use Illuminate\Foundation\Testing\WithoutMiddleware,
    Auth;

class UserTest extends TestCase
{
    use WithoutMiddleware;
    /**
     * Index action of User Controller test case
     *
     * @return void
     */
    public function testIndex()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\UserController@index');
        $this->assertResponseStatus($response->status());
        $this->assertResponseOk();
    }

    /**
     * indexData json action of User Controller test case
     *
     * @return void
     */
    public function testGetData()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\UserController@getData');
        $this->assertResponseStatus($response->status());

    }
    
    /**
     * create action of User Controller test case
     *
     * @return void
     */
    public function testCreate()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\UserController@create');
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
        $response = $this->action('POST', '\Modules\Admin\Http\Controllers\UserController@store',['username'=>'anaghaathale','email'=>'anaghaa+1@iprogrammer.com','first_name'=>'Test','last_name'=>'User','password'=>'anagha5t6y7u8i','password_confirmation'=>'anagha5t6y7u8i','gender'=>0,'contact'=>'9087654321','avatar'=>'','status'=>1,'skip_ip_check'=>'1','user_type_id'=>1,'remember_token'=>'NULL','deleted_at'=>'NULL','created_by'=>1,'created_at'=>date('Y-m-d h:i:s'),'updated_by'=>0,'updated_at'=>'']);
        $this->assertResponseStatus($response->status());
        $this->assertResponseOk();
    }
    
    /**
     * edit action of User Controller test case
     *
     * @return void
     */
    public function testEdit()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\UserController@edit', ['id' => 1]);
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
        $response = $this->action('PUT', '\Modules\Admin\Http\Controllers\UserController@update', ['id'=>5,'username'=>'anaghaathale1','email'=>'anaghaa+1@iprogrammer.com','first_name'=>'Test','last_name'=>'User','password'=>'anagha5t6y7u8i','password_confirmation'=>'anagha5t6y7u8i','gender'=>0,'contact'=>'9087654321','avatar'=>'','status'=>1,'skip_ip_check'=>'1','user_type_id'=>1,'remember_token'=>'NULL','deleted_at'=>'NULL','created_by'=>0,'created_at'=>'','updated_by'=>1,'updated_at'=>date('Y-m-d h:i:s')]);
        $this->assertResponseStatus($response->status());
    }
    
    /**
     * groupAction action of User Controller test case
     *
     * @return void
     */
    public function testGroupAction()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\UserController@getData');
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
            $keys = ['id', 'username', 'email', 'first_name', 'last_name'];

            foreach ($keys as $key) {
                $this->assertArrayHasKey($key, $responseJsonData['data'][0]);
            }
        }
    }
    
    
}
