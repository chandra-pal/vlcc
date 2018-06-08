<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon as Carbon;
use Modules\Admin\Models\SystemEmail;
use HTML;

class SystemEmailsTableSeeder extends Seeder
{

    public function __construct(SystemEmail $systemEmail)
    {
        $this->model = $systemEmail;
    }

    public function run()
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('system_emails')->truncate();

        $system_emails_array = [
            '0' => [
                'name' => 'UNAUTHORIZED_IP_ADDRESS',
                'description' => 'Login attempt from unauthorized IP address',
                'email_to' => 'webmaster@iprogrammer.com',
                'email_from' => 'iProgrammer Solutions <webmaster@iprogrammer.com>',
                'subject' => 'Login attempt from unauthorized IP address',
                'text1' => '
                    <p>
                        <span style = "font-family: arial,helvetica,sans-serif; font-size: small;">Hello, </span>
                    </p>
                    <p>
                        <span style = "font-family: arial,helvetica,sans-serif; font-size: small;"> 
                            There was an attempt to log into the admin section from an unauthorized IP Address, the details of which are as follows: 
                        </span> 
                        <br /><br /> 
                        <span style = "font-family: arial,helvetica,sans-serif; font-size: small;">
                            <strong>Username:</strong> {$USERNAME}
                        </span>
                        <br /> 
                        <span style = "font-family: arial,helvetica,sans-serif; font-size: small;">
                            <strong>IP Address:</strong> {$IPADDRESS}
                        </span>
                    </p>
                ',
                'text2' => '<p>'
                . '<span style="font-family: arial,helvetica,sans-serif; font-size: small;">'
                . '<strong>Thank You, </strong>'
                . '</span>'
                . '<br />'
                . '<span style="font-family: arial,helvetica,sans-serif; font-size: small;">'
                . 'Team {$C_SITENAME}'
                . '<br />'
                . '{$C_SITEURL}'
                . '</span>'
                . '</p>',
                'email_type' => '1',
                'created_by' => '1',
                'updated_by' => '1'
            ],
            '1' => [
                'name' => 'CHANGE_PASSWORD',
                'description' => 'Email to webmaster when an admin user changes his/her password',
                'email_to' => 'webmaster@iprogrammer.com',
                'email_from' => 'iProgrammer Solutions <webmaster@iprogrammer.com>',
                'subject' => 'Admin user changed password',
                'text1' => '
                    <p>
                        <span style="font-family: arial,helvetica,sans-serif; font-size: small;">Hello, </span>
                    </p>
                    <p>
                        <span style="font-family: arial,helvetica,sans-serif; font-size: small;"> The following user changed his/her password: </span> <br /><br /> <span style="font-family: arial,helvetica,sans-serif; font-size: small;"> <strong>Username:</strong> {$USERNAME} </span> <br /> 
                        <span style="font-family: arial,helvetica,sans-serif; font-size: small;"> <strong>IP Address:</strong> {$IPADDRESS} </span>
                    </p>
                ',
                'text2' => '<p>'
                . '<span style="font-family: arial,helvetica,sans-serif; font-size: small;">'
                . '<strong>Thank You, </strong>'
                . '</span>'
                . '<br />'
                . '<span style="font-family: arial,helvetica,sans-serif; font-size: small;">'
                . 'Team {$C_SITENAME}'
                . '<br />'
                . '{$C_SITEURL}'
                . '</span>'
                . '</p>',
                'email_type' => '1',
                'created_by' => '1',
                'updated_by' => '1'
            ]
        ];

        foreach ($system_emails_array as $system_email_item) {
            $systemEmail = new $this->model;

            $systemEmail->name = $system_email_item['name'];
            $systemEmail->description = $system_email_item['description'];
            $systemEmail->email_to = $system_email_item['email_to'];
            $systemEmail->email_from = $system_email_item['email_from'];
            $systemEmail->subject = $system_email_item['subject'];
            $systemEmail->text1 = HTML::entities($system_email_item['text1']);
            $systemEmail->text2 = HTML::entities($system_email_item['text2']);
            $systemEmail->email_type = $system_email_item['email_type'];
            $systemEmail->created_by = $system_email_item['created_by'];
            $systemEmail->created_at = Carbon::now();
            $systemEmail->updated_by = $system_email_item['updated_by'];
            $systemEmail->updated_at = Carbon::now();

            $save = $systemEmail->save();
        }


        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
