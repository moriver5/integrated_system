<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTipstersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipsters', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->String('name', 10)->nullable();
			$table->Text('contents')->nullable();		
			$table->tinyInteger('disp_flg')->unsigned()->default(0);
            $table->timestamps();
			
			$table->index('id', 'idx_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipsters');
    }
}
