<?php
/**
 * Test Case For CityController
 *
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Tests;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Auth;

class CityTest extends TestCase
{

    use WithoutMiddleware;

    public function setUp()
    {
        parent::setUp();
        Auth::loginUsingId(1);
    }

    /**
     * index action of CityController test case
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\CityController@index');
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\Response', $response);
    }

    /**
     * getData action of CityController test case
     *
     * @return void
     */
    public function testGetData()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\CityController@getData');
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
            $keys = ['id', 'country_id', 'states_id', 'name', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at', 'states', 'country'];
            foreach ($keys as $key) {
                $this->assertArrayHasKey($key, $responseJsonData['data'][0]);
            }

            if (!empty($responseJsonData['data'][0]['states'])) {
                $state_keys = ['id', 'name', 'country_id', 'state_code', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];
                foreach ($state_keys as $state_key) {
                    $this->assertArrayHasKey($state_key, $responseJsonData['data'][0]['states']);
                }
            }

            if (!empty($responseJsonData['data'][0]['country'])) {
                $country_keys = ['id', 'name', 'iso_code_2', 'iso_code_3', 'isd_code', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];
                foreach ($country_keys as $country_key) {
                    $this->assertArrayHasKey($country_key, $responseJsonData['data'][0]['country']);
                }
            }
        }
    }

    /**
     * getStateData action of CityController test case
     *
     * @return void
     */
    public function testGetStateData()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\CityController@getStateData');
        //dd($response->getContent());
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);

        $actual = $response->getContent();
        $responseJsonData = json_decode($actual, true);

        $this->assertArrayHasKey('list', $responseJsonData);
    }

    /**
     * create action of CityController test case
     *
     * @return void
     */
    public function testCreate()
    {

        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\CityController@create');
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\Response', $response);
    }

    /**
     * store action of CityController test case
     *
     * @return void
     */
    public function testStore()
    {
        $response = $this->action('POST', '\Modules\Admin\Http\Controllers\CityController@store');
        $this->assertResponseStatus(200 || 302, $response->status());

        if ($response->status() == 302) {
            $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        } else if ($response->status() == 200) {
            $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
        }
    }

    /**
     * edit action of CityController test case
     *
     * @return void
     */
    public function testEdit()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\CityController@edit', ['id' => '1']);
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
    }

    /**
     * update action of CityController test case
     *
     * @return void
     */
    public function testUpdate()
    {
        $response = $this->action('POST', '\Modules\Admin\Http\Controllers\CityController@update');
        $this->assertResponseStatus(200 || 302, $response->status());

        if ($response->status() == 302) {
            $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        } else if ($response->status() == 200) {
            $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
        }
    }
}
