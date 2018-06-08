<?php
/**
 * Test Case For FaqController
 *
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Tests;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Auth;

class FaqTest extends TestCase
{

    use WithoutMiddleware;

    public function setUp()
    {
        parent::setUp();
        Auth::loginUsingId(1);
    }

    /**
     * index action of FaqController test case
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\FaqController@index');
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\Response', $response);
    }

    /**
     * getData action of FaqController test case
     *
     * @return void
     */
    public function testGetData()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\FaqController@getData');
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
            $keys = ["id", "faq_category_id", "question", "answer", "position", "status", "created_by", "updated_by", "created_at", "updated_at", "question_answer", "action"];
            foreach ($keys as $key) {
                $this->assertArrayHasKey($key, $responseJsonData['data'][0]);
            }

            if (!empty($responseJsonData['data'][0]['faq_category'])) {

                $cat_keys = ["id", "name", "position", "status", "created_by", "updated_by", "created_at", "updated_at"];
                foreach ($cat_keys as $cat_key) {
                    $this->assertArrayHasKey($cat_key, $responseJsonData['data'][0]['faq_category']);
                }
            }
        }
    }

    /**
     * create action of FaqController test case
     *
     * @return void
     */
    public function testCreate()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\FaqController@create');
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\Response', $response);
    }

    /**
     * store action of FaqController test case
     *
     * @return void
     */
    public function testStore()
    {
        $insertData = ['faq_category_id' => '1', 'question' => 'Question', 'answer' => 'Answer', 'position' => '2', 'status' => 1];
        $response = $this->action('POST', '\Modules\Admin\Http\Controllers\FaqController@store', $insertData);
        $this->assertResponseStatus(200 || 302, $response->status());

        if ($response->status() == 302) {
            $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        } else if ($response->status() == 200) {
            $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
        }
    }

    /**
     * edit action of FaqController test case
     *
     * @return void
     */
    public function testEdit()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\FaqController@edit', ['id' => '1']);
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
    }

    /**
     * update action of FaqController test case
     *
     * @return void
     */
    public function testUpdate()
    {
        $updateData = ['id' => '1', 'faq_category_id' => '1', 'question' => 'Questions', 'answer' => 'Answer', 'position' => '4', 'status' => 1];
        $response = $this->action('PUT', '\Modules\Admin\Http\Controllers\FaqController@update', $updateData);

        $this->assertResponseStatus(200 || 302, $response->status());

        if ($response->status() == 302) {
            $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        } else if ($response->status() == 200) {
            $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
        }
    }
}
