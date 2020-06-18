<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('open_flg')->default(0);
            $table->string('title',100)->nullable();
            $table->string('name',100)->nullable();
            $table->string('msg')->nullable();
            $table->string('img')->nullable();
            $table->date('post_date')->nullable();
            $table->date('sort_date');
            $table->timestamps();
			
			$table->index('id', 'idx_id');
			$table->index('title', 'idx_title');
			$table->index('name', 'idx_name');
			$table->index('open_flg', 'idx_open_flg');
			$table->index('post_date', 'idx_post_date');
			$table->index('sort_date', 'idx_sort_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voices');
    }
}
