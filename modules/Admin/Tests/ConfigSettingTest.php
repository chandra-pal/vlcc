<?php
/**
 * Test Case For ConfigSettingController
 *
 *
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Tests;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Auth;

class ConfigSettingTest extends TestCase
{

    use WithoutMiddleware;

    public function setUp()
    {
        parent::setUp();
        Auth::loginUsingId(1);
    }

    /**
     * index action of ConfigSettingController test case
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\ConfigSettingController@index');
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\Response', $response);
    }

    /**
     * getData action of ConfigSettingController test case
     *
     * @return void
     */
    public function testGetData()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\ConfigSettingController@getData');
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);

        $actual = $response->getContent();
        $responseJsonData = json_decode($actual, true);

        $this->assertArrayHasKey('draw', $responseJsonData);
        $this->assertArrayHasKey('recordsTotal', $responseJsonData);
        $this->assertArrayHasKey('recordsFiltered', $responseJsonData);
        $this->assertArrayHasKey('data', $responseJsonData);
        $this->assertArrayHasKey('input', $responseJsonData);
        //dd($responseJsonData['data']);
        if (!empty($responseJsonData['data'])) {
            $keys = ['id', 'config_constant', 'config_value', 'description', 'config_category_id', 'created_by', 'updated_by', 'created_at', 'updated_at', 'config_category', 'action'];

            foreach ($keys as $key) {
                $this->assertArrayHasKey($key, $responseJsonData['data'][0]);
            }

            if (!empty($responseJsonData['data'][0]['config_category'])) {
                $cat_keys = ['id', 'category', 'position', 'created_by', 'updated_by', 'created_at', 'updated_at'];

                foreach ($cat_keys as $cat_key) {
                    $this->assertArrayHasKey($cat_key, $responseJsonData['data'][0]['config_category']);
                }
            }
        }
    }

    /**
     * create action of ConfigSettingController test case
     *
     * @return void
     */
    public function testCreate()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\ConfigSettingController@create');
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\Response', $response);
    }

    /**
     * store action of ConfigSettingController test case
     *
     * @return void
     */
    public function testStore()
    {
        $insertData = ['config_category_id' => '1', 'config_constant' => 'TEST_DATA', 'config_value' => 'test value', 'description' => 'Data inserted through test'];
        $response = $this->action('POST', '\Modules\Admin\Http\Controllers\ConfigSettingController@store', $insertData);
        $this->assertResponseStatus(200 || 302, $response->status());

        if ($response->status() == 302) {
            $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        } else if ($response->status() == 200) {
            $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
        }
    }

    /**
     * edit action of ConfigSettingController test case
     *
     * @return void
     */
    public function testEdit()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\ConfigSettingController@edit', ['id' => '1']);
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
    }

    /**
     * update action of ConfigSettingController test case
     *
     * @return void
     */
    public function testUpdate()
    {
        $response = $this->action('PUT', '\Modules\Admin\Http\Controllers\ConfigSettingController@update');
        $this->assertResponseStatus(200 || 302, $response->status());

        if ($response->status() == 302) {
            $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        } else if ($response->status() == 200) {
            $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
        }
    }
}
