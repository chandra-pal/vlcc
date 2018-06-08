<?php
/**
 * Test Case For UserTypeLinksController
 *
 *
 * @author Anagha Athale <anaghaa@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Tests;
use Illuminate\Foundation\Testing\WithoutMiddleware,
    Auth;


class UsertypelinksTest extends TestCase
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
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\UserTypeLinksController@index');
        $this->assertResponseStatus($response->status());
        $this->assertResponseOk();
        
    }

    /**
     * GetData action of LinkCategory Controller test case
     *
     * @return void
     */
    public function testStore()
    {
        Auth::loginUsingId(1);
        $response = $this->action('POST', '\Modules\Admin\Http\Controllers\UserTypeLinksController@store', ['links' => [6],'type_id' => 3]);
        $this->assertResponseStatus(200, $response->status());
        $this->assertResponseOk();
    }

    /**
     * Edit action of LinkCategory Controller test case
     *
     * @return void
     */
    public function testEdit()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\UserTypeLinksController@edit');
        $this->assertResponseStatus($response->status());
    }
}
