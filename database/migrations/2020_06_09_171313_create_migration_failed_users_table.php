<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMigrationFailedUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('migration_failed_users', function (Blueprint $table) {
            $table->bigIncrements('id');
			$table->integer('client_id')->nullable();
			$table->integer('login_id')->nullable();
			$table->string('email')->nullable();
			$table->tinyInteger('status')->nullable();
			$table->tinyInteger('is_quit')->nullable();
			$table->tinyInteger('disable')->nullable();
			$table->dateTime('reg_date')->nullable();
			$table->dateTime('last_access_date')->nullable();
			$table->string('memo')->nullable();
            $table->timestamps();
			
			$table->index('client_id', 'idx_client_id');
			$table->index('login_id', 'idx_login_id');
			$table->index('email', 'idx_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('migration_failed_users');
    }
}
