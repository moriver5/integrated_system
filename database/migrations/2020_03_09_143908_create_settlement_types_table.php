<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettlementTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settlement_types', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->string('name');						//決済会社名
			$table->integer('active')->default(0);		//無効：0 有効：1
			$table->string('clientip', 20);				//各決済会社のClientIP
			$table->integer('sendid_length');			//各決済会社でsendidの長さ
			$table->string('credit_url');				//各決済会社のクレジット決済URL
			$table->string('netbank_url');				//各決済会社のネットバンク決済URL
            $table->timestamps();

			$table->index('active', 'idx_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settlement_types');
    }
}
