<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('from_id');
            $table->string('from_type');
            $table->bigInteger('to_id');
            $table->string('to_type');
            $table->smallInteger('category_id');
            $table->string('url');
            $table->string('extra')->nullable();
            $table->tinyInteger('read')->default(0);
            $table->timestamps();

            $table->index('from_id');
            $table->index('from_type');
            $table->index('to_id');
            $table->index('to_type');
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
