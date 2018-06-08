<?php
/**
 * Event Listener for User activity actions
 *
 * @author Gaurav Patel <gauravp@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */
namespace App\Listeners;

use Spatie\Activitylog\Handlers\BeforeHandlerInterface;
use Log;

class ActivityEventListener implements BeforeHandlerInterface
{

    public function shouldLog($text, $userId)
    {   // Create exeception to not logged for followin userId
        if ($userId == 1)
            return false;

        return true;
    }
}
