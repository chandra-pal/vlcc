<?php
/**
 * Test Case For CountryController
 *
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Tests;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Auth;

class CountryTest extends TestCase
{

    use WithoutMiddleware;

    public function setUp()
    {
        parent::setUp();
        Auth::loginUsingId(1);
    }

    /**
     * index action of CountryController test case
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\CountryController@index');
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\Response', $response);
    }

    /**
     * getData action of CountryController test case
     *
     * @return void
     */
    public function testGetData()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\CountryController@getData');
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);

        $actual = $response->getContent();
        //dd($actual);
        $responseJsonData = json_decode($actual, true);

        $this->assertArrayHasKey('draw', $responseJsonData);
        $this->assertArrayHasKey('recordsTotal', $responseJsonData);
        $this->assertArrayHasKey('recordsFiltered', $responseJsonData);
        $this->assertArrayHasKey('data', $responseJsonData);
        $this->assertArrayHasKey('input', $responseJsonData);

        if (!empty($responseJsonData['data'])) {
            $keys = ['id', 'name', 'iso_code_2', 'iso_code_3', 'isd_code', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at', 'action'];

            foreach ($keys as $key) {
                $this->assertArrayHasKey($key, $responseJsonData['data'][0]);
            }
        }
    }

    /**
     * create action of CountryController test case
     *
     * @return void
     */
    public function testCreate()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\CountryController@create');
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\Response', $response);
    }

    /**
     * store action of CountryController test case
     *
     * @return void
     */
    public function testStore()
    {
        $insertData = ['name' => 'India', 'iso_code_2' => 'IN', 'iso_code_3' => 'IND', 'status' => '1'];
        $response = $this->action('POST', '\Modules\Admin\Http\Controllers\CountryController@store', $insertData);
        $this->assertResponseStatus(200 || 302, $response->status());

        if ($response->status() == 302) {
            $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        } else if ($response->status() == 200) {
            $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
        }
    }

    /**
     * edit action of CountryController test case
     *
     * @return void
     */
    public function testEdit()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\CountryController@edit', ['id' => '1']);
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
    }

    /**
     * update action of CountryController test case
     *
     * @return void
     */
    public function testUpdate()
    {
        $response = $this->action('PUT', '\Modules\Admin\Http\Controllers\CountryController@update', ['id' => '1', 'name' => 'Bharat', 'iso_code_2' => 'IN', 'iso_code_3' => 'IND', 'status' => '1']);
        $this->assertResponseStatus(200 || 302, $response->status());

        if ($response->status() == 302) {
            $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        } else if ($response->status() == 200) {
            $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
        }
    }
}
