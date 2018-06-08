<?php
/**
 * Event Listener for User Authentication attempt actions
 *
 * @author Gaurav Patel <gauravp@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Contracts\Mail\Mailer;
use Modules\Admin\Models\User;
use Modules\Admin\Repositories\IpAddressRepository;
use Modules\Admin\Repositories\IpLoginFailRepository;
use Mail;
use Log;
use Modules\Admin\Repositories\SystemEmailRepository;
use Modules\Admin\Models\SystemEmail;

class UserEventListener
{

    /**
     * The IpAddressRepository Repository instance.
     *
     * @var Modules\Admin\Repositories\IpAddressRepository
     */
    protected $ipAddressRepository;

    /**
     * The IpLoginFailRepository Repository instance.
     *
     * @var Modules\Admin\Repositories\IpLoginFailRepository
     */
    protected $ipLoginFailRepository;

    /**
     * Create a new.
     *
     * @param  Modules\Admin\Repositories\IpAddressRepository $ipAddressRepository
     * @param  Modules\Admin\Repositories\IpLoginFailRepository $ipLoginFailRepository
     * @return void
     */
    public function __construct(IpAddressRepository $ipAddressRepository, IpLoginFailRepository $ipLoginFailRepository)
    {
        $this->ipAddressRepository = $ipAddressRepository;
        $this->ipLoginFailRepository = $ipLoginFailRepository;
    }

    /**
     * Handle user login events.
     *
     * @return void
     */
    public function onUserLogin($event)
    {
        //\Log::info("User Logged In: " . $event->email);
    }

    /**
     * Handle user logout events.
     *
     * @return void
     */
    public function onUserLogout($event)
    {
        //\Log::info("User Logged Out: " . $event->email);
    }

    /**
     * Handle user login Attempt events.
     *
     * @return void
     */
    public function onUserLoginAttempt($event)
    {
        //\Log::info("User Login Attempt: " . $event['email']);
    }

    /**
     * Handle user login Attempt events.
     *
     * @return void
     */
    public function onIpAddressFailAttempt($event)
    {
        Log::info("User Login Attempt with Invlaid Ip address. Username: {$event['username']}, Ip-address : {$event['ip_address']} ");
        $this->ipAddressRepository->invalidIpAddressAttempt($event);
        $this->ipLoginFailRepository->invalidIpAddressAttempt($event);

        $emailcontent['IPADDRESS'] = $event['ip_address'];
        $emailcontent['USERNAME'] = $event['username'];
        $vars = $emailcontent;
        //dd($emailcontent);
        $systemEmailRepository = new SystemEmailRepository(new SystemEmail);
        return $emailcontent = $systemEmailRepository->sendEmail('UNAUTHORIZED_IP_ADDRESS', $vars);
//        Mail::send('admin::emails.auth.FailIpAttempt', $emailcontent, function($message) use($emailcontent) {
//            $message->to($emailcontent['email_to'], $emailcontent['name'])
//                ->subject($emailcontent['subject']);
//        });
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        $events->listen(
            'auth.login', 'Modules\Admin\Listeners\UserEventListener@onUserLogin'
        );

        $events->listen(
            'auth.logout', 'Modules\Admin\Listeners\UserEventListener@onUserLogout'
        );

        $events->listen(
            'auth.attempt', 'Modules\Admin\Listeners\UserEventListener@onUserLoginAttempt'
        );

        $events->listen(
            'ipaddressfail.attempt', 'Modules\Admin\Listeners\UserEventListener@onIpAddressFailAttempt'
        );
    }
}
