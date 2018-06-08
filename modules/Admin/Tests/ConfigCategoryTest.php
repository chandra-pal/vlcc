<?php
/**
 * Test Case For ConfigCategoryController
 *
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Tests;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Auth;

class ConfigCategoryTest extends TestCase
{

    use WithoutMiddleware;

    public function setUp()
    {
        parent::setUp();
        Auth::loginUsingId(1);
    }

    /**
     * index action of ConfigCategoryController test case
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\ConfigCategoryController@index');
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\Response', $response);
    }

    /**
     * getData action of ConfigCategoryController test case
     *
     * @return void
     */
    public function testGetData()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\ConfigCategoryController@getData');
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

    /**
     * create action of ConfigCategoryController test case
     *
     * @return void
     */
    public function testCreate()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\ConfigCategoryController@create');
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\Response', $response);
    }

    /**
     * store action of ConfigCategoryController test case
     *
     * @return void
     */
    public function testStore()
    {
        $insertData = ['category' => 'Extra Settings'];
        $response = $this->action('POST', '\Modules\Admin\Http\Controllers\ConfigCategoryController@store', $insertData);
        $this->assertResponseStatus(200 || 302, $response->status());

        if ($response->status() == 302) {
            $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        } else if ($response->status() == 200) {
            $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
        }
    }

    /**
     * edit action of ConfigCategoryController test case
     *
     * @return void
     */
    public function testEdit()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\ConfigCategoryController@edit', ['id' => '1']);
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
    }

    /**
     * update action of ConfigCategoryController test case
     *
     * @return void
     */
    public function testUpdate()
    {
        $response = $this->action('PUT', '\Modules\Admin\Http\Controllers\ConfigCategoryController@update', ['id' => '1', 'category' => 'General Settings']);
        $this->assertResponseStatus(200 || 302, $response->status());

        if ($response->status() == 302) {
            $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        } else if ($response->status() == 200) {
            $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
        }
    }
}
