<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperationDbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operation_dbs', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->String('name', 20);							//表示名
			$table->String('db', 20);							//DB名
			$table->String('host', 20)->nullable();				//ホスト名
			$table->String('port', 15)->nullable();				//ポート番号
			$table->String('username', 20)->nullable();			//ユーザーID
			$table->String('password', 30)->nullable();			//パスワード
            $table->timestamps();

			$table->index('db', 'idx_db');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operation_dbs');
    }
}
