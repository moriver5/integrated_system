<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAchievementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('achievements', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('type')->default(0);		//0:商品選択　1:予想情報選択
            $table->integer('product_id');
            $table->integer('priority_id')->default(1);
            $table->integer('open_flg')->default(0);
            $table->date('race_date');
            $table->string('race_name',100);
            $table->string('msg1')->nullable();
            $table->string('msg2')->nullable();
            $table->string('msg3')->nullable();
            $table->string('memo')->nullable();
            $table->date('sort_date');
            $table->timestamps();
			
			$table->index('id', 'idx_id');
			$table->index('race_name', 'idx_race_name');
			$table->index('priority_id', 'idx_priority_id');
			$table->index('race_date', 'idx_race_date');
			$table->index('open_flg', 'idx_open_flg');
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
        Schema::dropIfExists('achievements');
    }
}
