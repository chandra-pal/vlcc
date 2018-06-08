<?php
/**
 * Test Case For MyProfileController
 *
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Tests;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Auth;

class MyProfileTest extends TestCase
{

    use WithoutMiddleware;

    public function setUp()
    {
        parent::setUp();
        Auth::loginUsingId(1);
    }

    /**
     * index action of MyProfileController test case
     *
     * @return void
     */
    public function testIndex()
    {
        $response = $this->action('GET', '\Modules\Admin\Http\Controllers\MyProfileController@index');
        $this->assertResponseStatus(200, $response->status());
        $this->assertInstanceOf('Illuminate\Http\Response', $response);
    }

    /**
     * update action of MyProfileController test case
     *
     * @return void
     */
    public function testUpdate()
    {
        $updateData = ['email' => 'user@user.com', 'first_name' => 'userfirst', 'last_name' => 'userlast', 'password' => '8i9o0p-[', 'gender' => '1', 'contact' => '9595959595', 'user_type_id' => '2'];
        $response = $this->action('PUT', '\Modules\Admin\Http\Controllers\MyProfileController@update', $updateData);
        $this->assertResponseStatus(200 || 302, $response->status());

        if ($response->status() == 302) {
            $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        } else if ($response->status() == 200) {
            $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
        }
    }

    /**
     * updateAvatar action of MyProfileController test case
     *
     * @return void
     */
    public function testUpdateAvatar()
    {
        $response = $this->action('PUT', '\Modules\Admin\Http\Controllers\MyProfileController@updateAvatar');
        $this->assertResponseStatus(200 || 302, $response->status());

        if ($response->status() == 302) {
            $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        } else if ($response->status() == 200) {
            $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
        }
    }

    /**
     * changePassword action of MyProfileController test case
     *
     * @return void
     */
    public function testChangePassword()
    {
        $updateData = ['id' => '3', 'current_password' => '8i9o0p-[', 'password' => '7u8i9o0p', 'password_confirm' => '7u8i9o0p'];

        $response = $this->action('PUT', '\Modules\Admin\Http\Controllers\MyProfileController@changePassword', $updateData);

        $this->assertResponseStatus(200 || 302, $response->status());

        if ($response->status() == 302) {
            $this->assertInstanceOf('Illuminate\Http\RedirectResponse', $response);
        } else if ($response->status() == 200) {
            $this->assertInstanceOf('Illuminate\Http\JsonResponse', $response);
        }
    }
}
