<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSystemEmails extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('system_emails', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name')->unique()->index();
            $table->string('description')->nullable();
            $table->text('email_to')->nullable();
            $table->text('email_cc')->nullable();
            $table->text('email_bcc')->nullable();
            $table->string('email_from', 100);
            $table->string('subject', 255);
            $table->text('text1');
            $table->text('text2');
            $table->boolean('email_type')->unsigned()->comment = "1 : Email to Admin, 2 : Email to User, 3 : Email to Others";
            $table->boolean('status')->unsigned()->default(true)->comment = "1 : Active, 0 : Inactive";
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('system_emails');
    }

}
