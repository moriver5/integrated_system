<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRegisteredMailQueuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('registered_mail_queues', function (Blueprint $table) {
            $table->integer('send_id');
            $table->string('ad_cd')->nullable();
            $table->integer('client_id');
            $table->string('mail');
            $table->integer('group_id')->nullable();
            $table->integer('device');					//PC：2 携帯：1
			$table->timestamps();

			$table->unique(['send_id','mail']);
			$table->index('ad_cd', 'idx_ad_cd');
			$table->index('client_id', 'idx_client_id');
			$table->index('mail', 'idx_mail');
			$table->index('group_id', 'idx_group_id');
			$table->index('device', 'idx_device');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registered_mail_queues');
    }
}
