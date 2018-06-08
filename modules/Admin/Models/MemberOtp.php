<?php

/**
 * The class to present MemberOtp model.
 * 
 * 
 * @author Gauri Deshmukh <gaurid@iprogrammer.com>
 * @package Admin
 * @since 1.0
 */

namespace Modules\Admin\Models;

class MemberOtp extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'member_otp';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['mobile_number', 'otp', 'sms_delivered', 'error_message', 'otp_used', 'platform_generated_for', 'attempt_count', 'otp_generated_for'];

}
