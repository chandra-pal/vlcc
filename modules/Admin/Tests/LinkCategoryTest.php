<?php
/**
 * Test Case For LinkCategory Controller
 *
 *
 * @author Anagha Athale <anaghaa@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Tests;

use Illuminate\Foundation\Testing\WithoutMiddleware,
    Auth;

class LinkCategoryTest extends TestCase
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
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\LinkCategoryController@index');
        $this->assertResponseStatus($response->status());
        $this->assertResponseOk();
    }

    /**
     * Index action of LinkCategory Controller test case
     *
     * @return void
     */
    public function testGetData()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\LinkCategoryController@getData');
        $this->assertResponseStatus($response->status());
        $this->assertResponseOk();
    }

    /**
     * create action of LinkCategory Controller test case
     *
     * @return void
     */
    public function testCreate()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\LinkCategoryController@create');
        $this->assertResponseStatus($response->status());
        $this->assertResponseOk();
    }

    /**
     * store action of LinkCategory Controller test case
     *
     * @return void
     */
    public function testStore()
    {
        Auth::loginUsingId(1);
        $response = $this->action('POST', '\Modules\Admin\Http\Controllers\LinkCategoryController@store', ['category' => 'test', 'header_text' => 'test description', 'position' => 50, 'status' => 1, 'created_by' => 1, 'updated_by' => 0, 'created_at' => date('Y-m-d h:i:s'), 'updated_at' => '', 'category_icon' => 'test icon']);
        $this->assertResponseStatus(302 || 200, $response->status());
        $this->assertResponseOk();
    }

    /**
     * edit action of LinkCategory Controller test case
     *
     * @return void
     */
    public function testEdit()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\LinkCategoryController@edit', ['id' => 16]);
        $this->assertResponseStatus(200, $response->status());
        $this->assertResponseOk();
    }

    /**
     * update action of LinkCategory Controller test case
     *
     * @return void
     */
    public function testUpdate()
    {
        Auth::loginUsingId(1);
        $response = $this->action('PUT', '\Modules\Admin\Http\Controllers\LinkCategoryController@update', ['id' => 15, 'category' => 'test updated', 'header_text' => 'test description', 'position' => 50, 'status' => 1, 'created_by' => 0, 'updated_by' => 1, 'created_at' => '', 'updated_at' => date('Y-m-d h:i:s'), 'category_icon' => 'test-icon']);
        $this->assertResponseStatus($response->status());
    }

    /**
     * groupAction action of LinkCategory Controller test case
     *
     * @return void
     */
    public function testGroupAction()
    {
        Auth::loginUsingId(1);
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\LinkCategoryController@getData');
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
            $keys = ['id', 'category', 'position', 'created_by', 'action'];

            foreach ($keys as $key) {
                $this->assertArrayHasKey($key, $responseJsonData['data'][0]);
            }
        }
    }
}
