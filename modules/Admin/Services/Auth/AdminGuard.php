<?php
/**
 * To Extend Auth Guard with Admin Module
 *
 * @author Gaurav Patel <gauravp@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace Modules\Admin\Services\Auth;

use Illuminate\Auth\Guard;

class AdminGuard extends Guard
{
    /**
     * Get a unique identifier for the admin auth session value.
     *
     * @return string
     */
    public function getName()
    {
        return 'admin_login_' . md5(get_class($this));
    }

    /**
     * Get the name of the cookie used to store the "recaller".
     *
     * @return string
     */
    public function getRecallerName()
    {
        return 'admin_remember_' . md5(get_class($this));
    }
}
