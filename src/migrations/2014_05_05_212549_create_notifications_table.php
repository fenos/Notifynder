<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('from_id');
            $table->string('from_type');
            $table->integer('to_id');
            $table->string('to_type');
            $table->integer('category_id');
            $table->string('url');
            $table->string('extra');
            $table->integer('read');
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
        Schema::drop('notifications');
    }

}
