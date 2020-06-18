<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('login_id', 254)->unique()->nullable();
            $table->string('password', 255)->nullable();
            $table->string('password_raw', 255)->nullable();
            $table->string('mail_address')->unique()->nullable();
			$table->string('mobile_mail_address', 255)->unique()->nullable();
			$table->string('identify_id', 10)->nullable();
			$table->string('session_id', 20)->nullable();
            $table->rememberToken();
			$table->tinyInteger('mail_status')->default(1);
			$table->tinyInteger('status')->default(0);
			$table->unsignedInteger('point')->default(0);
			$table->integer('pay_count')->default(0);
			$table->integer('pay_amount')->default(0);
			$table->integer('group_id')->default(0);
			$table->string('ad_cd', 255)->nullable()->default('');
			$table->string('credit_certify_phone_no', 255)->nullable();
			$table->tinyInteger('bonus_flg')->default(0);
			$table->Integer('action')->default(0);
			$table->dateTime('pay_datetime')->nullable();
			$table->bigInteger('sort_pay_datetime')->nullable();
			$table->dateTime('last_access_datetime')->nullable();
			$table->bigInteger('sort_last_access_datetime')->nullable();
			$table->dateTime('quit_datetime')->nullable();
			$table->bigInteger('sort_quit_datetime')->nullable();
			$table->dateTime('temporary_datetime')->nullable();
			$table->bigInteger('sort_temporary_datetime')->nullable();
			$table->text('description')->nullable();
			$table->bigInteger('regist_date')->nullable();
            $table->timestamps();
			$table->unsignedTinyInteger('disable')->default(0);
			
			$table->index('login_id', 'idx_login_id');
			$table->index('ad_cd', 'idx_ad_cd');
			$table->index('mail_address', 'idx_mail_address');
			$table->index('mobile_mail_address', 'idx_mobile_mail_address');
			$table->index('group_id', 'idx_group_id');
			$table->index('status', 'idx_status');
			$table->index('mail_status', 'idx_mail_status');
			$table->index('created_at', 'idx_created_at');
			$table->index('pay_datetime', 'idx_pay_datetime');
			$table->index('sort_pay_datetime', 'idx_sort_pay_datetime');
			$table->index('temporary_datetime', 'idx_temporary_datetime');
			$table->index('sort_temporary_datetime', 'idx_sort_temporary_datetime');
			$table->index('last_access_datetime', 'idx_last_access_datetime');
			$table->index('sort_last_access_datetime', 'idx_sort_last_access_datetime');
			$table->index('quit_datetime', 'idx_quit_datetime');
			$table->index('sort_quit_datetime', 'idx_sort_quit_datetime');
			$table->index('pay_count', 'idx_pay_count');
			$table->index('pay_amount', 'idx_pay_amount');
			$table->index('point', 'idx_point');
			$table->index('remember_token', 'idx_remember_token');
			$table->index('regist_date', 'idx_regist_date');
			$table->index('disable', 'idx_disable');
			$table->index('bonus_flg', 'idx_bonus_flg');
			$table->index('action', 'idx_action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
