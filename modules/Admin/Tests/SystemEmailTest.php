<?php
/**
 * Test Case For SystemEmailController
 *
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Tests;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Auth;

class SystemEmailTest extends TestCase
{

    use WithoutMiddleware;

    public function setUp()
    {
        parent::setUp();
        Auth::loginUsingId(1);
    }

    /**
     * index action of SystemEmailController test case
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\SystemEmailController@index');
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\Response', $response);
    }

    /**
     * create action of SystemEmailController test case
     *
     * @return void
     */
    public function testCreate()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\SystemEmailController@create');
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\Response', $response);
    }

    /**
     * store action of SystemEmailController test case
     *
     * @return void
     */
    public function testStore()
    {
        $insertData = ['name' => 'TEST_EMAIL_TEMPLATE', 'description' => 'Test Description', 'email_to' => 'test@test.com', 'email_from' => 'from@test.com', 'subject' => 'Test Subject', 'email_type' => '1', 'text1' => '<b>Test Body</b>', 'text2' => '<i>Test Signature</i>'];
        $response = $this->action('POST', '\Modules\Admin\Http\Controllers\SystemEmailController@store', $insertData);

        $this->assertResponseStatus(200 || 302, $response->status());

        if ($response->status() == 302) {
            $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        } else if ($response->status() == 200) {
            $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
        }
    }

    /**
     * edit action of SystemEmailController test case
     *
     * @return void
     */
    public function testEdit()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\SystemEmailController@edit', ['id' => '1']);
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
    }

    /**
     * update action of SystemEmailController test case
     *
     * @return void
     */
    public function testUpdate()
    {
        $updateData = ['id' => '2', 'name' => 'TEST_EMAIL_TEMPLATE_UPDATED', 'description' => 'Test Description', 'email_to' => 'test@test.com', 'email_from' => 'from@test.com', 'subject' => 'Test Subject', 'email_type' => '1', 'text1' => '<b>Test Body</b>', 'text2' => '<i>Test Signature</i>', 'status' => '1'];
        $response = $this->action('PUT', '\Modules\Admin\Http\Controllers\SystemEmailController@update', $updateData);
        $this->assertResponseStatus(200 || 302, $response->status());

        if ($response->status() == 302) {
            $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        } else if ($response->status() == 200) {
            $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
        }
    }
}
