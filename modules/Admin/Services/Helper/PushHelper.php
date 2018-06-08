<?php

/**
 * The helper library class for user image processing ang geting
 *
 *
 * @author Nilesh Pangul <nileshp@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Services\Helper;

use DB;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use FCM;
use Config;

class PushHelper {

    public static function sendGeneralPushNotification($token, $tag, $message, $extra, $title, $deviceType, $id) {
        Config::get('fcm.http.server_key');
        Config::get('fcm.http.sender_id');

        $optionBuiler = new OptionsBuilder();
        //$optionBuiler->setTimeToLive(60 * 20); // in sec
        $optionBuiler->setTimeToLive(86400); // 1 day

        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($message)
                ->setTitle($title)
                ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['id' => $id, 'extra' => $extra, 'tag' => $tag]);

        $option = $optionBuiler->build();
        $notification = NULL;
        if ($deviceType == 2) {
            $notification = $notificationBuilder->build();
        }
        $data = $dataBuilder->build();

        // You must change it to get your tokens
        //$tokens = MYDATABASE::pluck('fcm_token')->toArray();
        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

        //return Array - you must remove all this tokens in your database
        $tokensToDelete = $downstreamResponse->tokensToDelete();

        //return Array (key : oldToken, value : new token - you must change the token in your database )
        $tokensToModify = $downstreamResponse->tokensToModify();

        //return Array - you should try to resend the message to the tokens in the array
        $tokensToRetry = $downstreamResponse->tokensToRetry();

        // return Array (key:token, value:errror) - in production you should remove from your database the tokens present in this array
        $tokensWithError = $downstreamResponse->tokensWithError();

        $errorsTokens = array_merge($tokensToDelete, $tokensWithError, $tokensToModify, $tokensToRetry);

        $logger = new Logger('Laravel-FCM-Errors');
        $logger->pushHandler(new StreamHandler(storage_path('logs/laravel-fcm-errors.log')));
        $logMessage = PHP_EOL . "Failed Tokens with Errors: " . json_encode($errorsTokens) . PHP_EOL;
        $logger->info($logMessage);
    }

}
