<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberFoodsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_foods', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->string('diet_item', 50)->index();
            $table->string('measure', 50);
            $table->integer('calories')->unsigned();
            $table->integer('serving_size')->unsigned();
            $table->string('serving_unit', 20);
            $table->foreign('member_id')->references('id')->on('members');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('member_foods', function($table) {
            $table->dropForeign('member_foods_member_id_foreign');
            $table->dropIndex('member_foods_member_id_foreign');
            $table->dropColumn('member_id');
        });
        Schema::drop('member_foods');
    }
}
