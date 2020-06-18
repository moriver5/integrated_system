<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\User;
use App\Mail\SendMail;
use Mail;
use Utility;

class MailDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:delivery {db_name} {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '予想一覧→アクセス一覧の全員にメール配信';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		//予想IDを条件に配信先メールアドレス取得
		$db_obj = new User;
		$db_obj->setConnection($this->argument('db_name'));
		$db_data = $db_obj->join('visitor_logs', 'users.id', '=', 'visitor_logs.client_id')->where('forecast_id', $this->argument('id'))->get();

		if( !empty($db_data) ){
			//smtp取得
			list($options['host_ip'], $options['port']) = Utility::getSmtpHost('melmaga');

			//
			$options['client_id'] = $db_data->id;

			foreach($db_data as $lines){
				//1秒待機
				usleep(1000000);

				$err_flg = Utility::checkNgWordEmail($lines->mail_address, $db_name);

				//禁止ワードが含まれていたら
				if( !is_null($err_flg) ){
					continue;
				}

				//メール送信
				Mail::to($lines->mail_address)->queue( new SendMail($options, $data) );
			}
		}
    }
}
