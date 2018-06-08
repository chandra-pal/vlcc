<?php
/**
 * Test Case For ManagePagesController
 *
 *
 * @author Anagha Athale <anaghaa@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Tests;

use Illuminate\Foundation\Testing\WithoutMiddleware,
    Auth;

class Pages extends TestCase
{

    use WithoutMiddleware;

    /**
     * Index action of ManagePagesController test case
     *
     * @return void
     */
    public function testIndex()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\ManagePagesController@index');
        $this->assertResponseStatus($response->status());
        $this->assertResponseOk();
    }

    /**
     * GetData action of ManagePagesController test case
     *
     * @return void
     */
    public function testGetData()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\ManagePagesController@getData');
        $this->assertResponseStatus($response->status());
        $this->assertResponseOk();
    }

    /**
     * create action of ManagePagesController test case
     *
     * @return void
     */
    public function testCreate()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\ManagePagesController@create');
        $this->assertResponseStatus($response->status());
        $this->assertResponseOk();
    }

    /**
     * store action of ManagePagesController test case
     *
     * @return void
     */
    public function testStore()
    {
        Auth::loginUsingId(1);
        $response = $this->action('POST', '\Modules\Admin\Http\Controllers\ManagePagesController@store', ['page_name' => 'Test page', 'page_url' => 'admin.test.test', 'slug' => 'test......', 'page_desc' => 'Testing going on please ignore', 'browser_title' => 'Test Header', 'meta_keywords' => 'Test..........', 'meta_description' => 'Test......', 'page_content' => 'Test......Test......', 'status' => 1, 'created_by' => 1, 'updated_by' => 0, 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => '']);
        $this->assertResponseStatus(302 || 200, $response->status());
        $this->assertResponseOk();
    }

    /**
     * edit action of Links Controller test case
     *
     * @return void
     */
    public function testEdit()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\ManagePagesController@edit', ['id' => 1]);
        $this->assertResponseStatus(200, $response->status());
        $this->assertResponseOk();
    }

    /**
     * update action of Links Controller test case
     *
     * @return void
     */
    public function testUpdate()
    {
        Auth::loginUsingId(1);
        $response = $this->action('PUT', '\Modules\Admin\Http\Controllers\ManagePagesController@update', ['id' => 2, 'page_name' => 'Test page updated', 'page_url' => 'admin.test.test', 'slug' => 'test......', 'page_desc' => 'Testing going on please ignore', 'browser_title' => 'Test Header', 'meta_keywords' => 'Test..........', 'meta_description' => 'Test......', 'page_content' => 'Test......Test......', 'status' => 1, 'created_by' => 0, 'updated_by' => 1, 'created_at' => '', 'updated_at' => date('Y-m-d h:i:s')]);
        $this->assertResponseStatus($response->status());
    }

    /**
     * groupAction action of Links Controller test case
     *
     * @return void
     */
    public function testGroupAction()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\ManagePagesController@getData');
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
            $keys = ['id', 'page_name', 'page_url', 'created_by'];

            foreach ($keys as $key) {
                $this->assertArrayHasKey($key, $responseJsonData['data'][0]);
            }
        }
    }
}
