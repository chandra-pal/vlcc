<?php
/**
 * The repository class for managing system email specific actions.
 * 
 * 
 * @author Tushar Dahiwale <tushard@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Repositories;

use Modules\Admin\Models\SystemEmail;
use Exception;
use Route;
use Log;
use Cache;
use Mail;

class SystemEmailRepository extends BaseRepository
{

    /**
     * Create a new model SystemEmail instance.
     *
     * 
     * @param Modules\Admin\Models\ConfigCategory $configCategory
     * @return void
     */
    public function __construct(SystemEmail $systemEmail)
    {
        $this->model = $systemEmail;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function data($params = [])
    {
        $response = [];
        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
//Cache::tags not suppport with files and Database
        $response = Cache::tags(SystemEmail::table())->remember($cacheKey, $this->ttlCache, function() {
            return $allEmails = SystemEmail::all();
        });

        return $response;
    }

    /**
     * List of available email types
     * 
     * @return response
     */
    public function emailTypesData()
    {
        $emailTo = ['1' => 'Email To Admin', '2' => 'Email To User', '3' => 'Email To Other'];

        return $emailTo;
    }

    /**
     * Send an email using specified email template
     * 
     * @return response
     */
    public function sendEmail($email_teplate_name, $vars, $params = [])
    {
        $replacements = [];
        $patterns = [];

        $cacheKey = str_replace(['\\'], [''], __METHOD__) . ':' . md5(json_encode($params));
//Cache::tags not suppport with files and Database
        $emailContent = Cache::tags(SystemEmail::table())->remember($cacheKey, $this->ttlCache, function() use($email_teplate_name) {
            return SystemEmail::whereName($email_teplate_name)->firstOrFail()->toArray();
        });

//email message

        $emailMessage = $emailContent['text1'];
        foreach ($vars as $key => $var) {
            $emailMessage = preg_replace('/{\$(' . preg_quote($key) . ')}/i', $var, $emailMessage);
        }

        preg_match_all('/{\$\\S+}/i', $emailMessage, $matches);

        foreach ($matches[0] as $key => $match) {
            $matchName = str_replace('{$', '', (str_replace('}', '', $match)));
            $patterns[$key] = '/{\$\\' . $matchName . '}/'; //str_replace(':', '', $match);
            $replacements[$key] = config('settings.' . $matchName);
            if (!$replacements[$key]) {
                $replacements[$key] = str_replace(':', '', $match);
            }
        }

        $emailMessageNew = preg_replace($patterns, $replacements, $emailMessage);


//email signature

        $emailSignature = $emailContent['text2'];
        foreach ($vars as $key => $var) {
            $emailSignature = preg_replace('/{\$(' . preg_quote($key) . ')}/i', $var, $emailSignature);
        }

        preg_match_all('/{\$\\S+}/i', $emailSignature, $matches);

        foreach ($matches[0] as $key => $match) {
            $matchName = str_replace('{$', '', (str_replace('}', '', $match)));
            $patterns[$key] = '/{\$\\' . $matchName . '}/'; //str_replace(':', '', $match);
            $replacements[$key] = config('settings.' . $matchName);
            if (!$replacements[$key]) {
                $replacements[$key] = str_replace(':', '', $match);
            }
        }

        $emailSignatureNew = preg_replace($patterns, $replacements, $emailSignature);

//subject

        $emailSubject = $emailContent['subject'];
        foreach ($vars as $key => $var) {
            $emailSubject = preg_replace('/{\$(' . preg_quote($key) . ')}/i', $var, $emailSubject);
        }

        preg_match_all('/{\$\\S+}/i', $emailSubject, $matches);

        foreach ($matches[0] as $key => $match) {
            $matchName = str_replace('{$', '', (str_replace('}', '', $match)));
            $patterns[$key] = '/{\$\\' . $matchName . '}/'; //str_replace(':', '', $match);
            $replacements[$key] = config('settings.' . $matchName);
            if (!$replacements[$key]) {
                $replacements[$key] = str_replace(':', '', $match);
            }
        }

        $emailSubjectNew = preg_replace($patterns, $replacements, $emailSubject);


        $emailContent['text1'] = $emailMessageNew;
        $emailContent['text2'] = $emailSignatureNew;
        $emailContent['subject'] = $emailSubjectNew;
        if ($emailContent['email_cc']) {
            $emailContent['email_cc'] = explode(',', $emailContent['email_cc']);
        }
        if ($emailContent['email_bcc']) {
            $emailContent['email_bcc'] = explode(',', $emailContent['email_bcc']);
        }


        if (str_contains($emailContent['email_from'], '<')) {
            $str = explode('<', $emailContent['email_from']);
            $email = preg_replace('/\>/', '', $str[1]);
            $name = $str[0];
            $email = filter_var($email, FILTER_VALIDATE_EMAIL);
            $emailContent['email_from'] = [$email => $name];
        }

        if (!empty($emailContent['email_to']) || !empty(config('settings.C_CS_EMAILID'))) {
            Mail::send('admin::emails.auth.FailIpAttempt', ['emailContent' => $emailContent], function($message) use($emailContent) {

                $message->subject($emailContent['subject']);

                if (!empty($emailContent['email_cc'])) {
                    $message->cc($emailContent['email_cc']);
                }

                if (!empty($emailContent['email_bcc'])) {
                    $message->bcc($emailContent['email_bcc']);
                }

                if (!empty($emailContent['email_to'])) {
                    $message->to($emailContent['email_to']);
                } else if (!empty(config('settings.C_CS_EMAILID'))) {
                    $message->to(config('settings.C_CS_EMAILID'));
                } else {
                    return $response['message'] = trans('admin::controller/system-email.not-sent');
                }

                if (!empty($emailContent['email_from'])) {
                    $message->from($emailContent['email_from']);
                }
            });

            if (count(Mail::failures()) > 0) {
                $response['message'] = trans('admin::controller/system-email.not-sent');
                foreach (Mail::failures as $email_address) {
                    $response['message'] .= $email_address;
                }
                return $response;
            } else {
                return $response['message'] = trans('admin::controller/system-email.sent');
            }
        } else {
            return $response['message'] = trans('admin::controller/system-email.not-sent');
        }



        //echo view('admin::emails.auth.FailIpAttempt', compact('emailContent'))->render();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Form data posted from ajax $inputs
     * @return $result array with status and message elements
     */
    public function create($inputs)
    {
        try {
            $systemEmail = new $this->model;

            $allColumns = $systemEmail->getTableColumns($systemEmail->getTable());

            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $systemEmail->$key = $value;
                }
            }

            $save = $systemEmail->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.added', ['name' => 'System Email']);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-added', ['name' => 'System Email']);
            }

            return $response;
        } catch (Exception $e) {
            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-added', ['name' => 'System Email']) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error(trans('admin::messages.not-added', ['name' => 'System Email']), ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }

    /**
     * Update a configuration settting.
     *
     * @param  Form data posted from ajax $inputs, Modules\Admin\Models\ConfigSetting $configSetting
     * @return $result array with status and message elements
     */
    public function update($inputs, $systemEmail)
    {
        try {
            $allColumns = $systemEmail->getTableColumns($systemEmail->getTable());

            foreach ($inputs as $key => $value) {
                if (in_array($key, $allColumns)) {
                    $systemEmail->$key = $value;
                }
            }

            $save = $systemEmail->save();

            if ($save) {
                $response['status'] = 'success';
                $response['message'] = trans('admin::messages.updated', ['name' => 'System Email']);
            } else {
                $response['status'] = 'error';
                $response['message'] = trans('admin::messages.not-updated', ['name' => 'System Email']);
            }

            return $response;
        } catch (Exception $e) {

            $exceptionDetails = $e->getMessage();
            $response['status'] = 'error';
            $response['message'] = trans('admin::messages.not-updated', ['name' => 'System Email']) . "<br /><b> Error Details</b> - " . $exceptionDetails;
            Log::error("System Email could not be updated.", ['Error Message' => $exceptionDetails, 'Current Action' => Route::getCurrentRoute()->getActionName()]);

            return $response;
        }
    }
}
