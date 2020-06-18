<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Mail\SendMail;
use App\Model\User;
use App\Model\Registered_mail_queue;
use Mail;
use Carbon\Carbon;
use DB;
use Utility;
use Session;

class RegisteredSendMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'registered:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '登録後送信メールの送信';

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
		//現在時刻
		$now_date = Carbon::now();

		//サイトごとのDB名リストを取得
		$listSiteDb = DB::select("select db from operation_dbs");

		if( count($listSiteDb) > 0 ){
			foreach($listSiteDb as $db_lines){
				$db_name = $db_lines->db;

				//有効の登録後送信メールのデータ取得
				$db_data = DB::connection($db_name)->select("select * from registered_mails inner join registered_mail_queues on registered_mails.id = registered_mail_queues.send_id where enable_flg = 1 and title != '' and ( body != '' or html_body != '' )");
//error_log(print_r($db_data,true).":{$db_name}\n",3,"/data/www/storage/siteo/storage/logs/nishi_log.txt");

				//登録後送信メールのデータがあれば
				if( !empty($db_data) ){
					foreach($db_data as $outer_lines){
						$db_reg_obj = new Registered_mail_queue;
						$db_reg_obj->setConnection($db_name);
						$db_mail = $db_reg_obj->where(function($query) use($outer_lines) {
							$query->where('send_id', $outer_lines->id);

							//抽出項目に値が入力されていれば
							if( !empty($outer_lines->item_value) ){
								$query->where(config('const.registered_send_item')[$outer_lines->item_type], config('const.registered_like_type')[$outer_lines->like_type], $outer_lines->item_value);
							}

							//グループ
							if( !empty($outer_lines->groups) ){
								$query->whereIn('group_id', explode(",", $outer_lines->groups));
							}

							//送信端末
							if( !empty($outer_lines->device) ){
								$query->where('device', $outer_lines->device);
							}

							//現在時刻から指定時間を引く
							$dt = new Carbon();
							$interval_date = $dt->subSecond($outer_lines->specified_time * 60);

							//指定時間経過
							$query->where("created_at", '<=', $interval_date);
//							$sql = $query->where("created_at", '<=', $interval_date)->toSql();
//error_log("{$sql}\n",3,"/data/www/storage/siteo/storage/logs/nishi_log.txt");
							return $query;
						})->get();
//error_log(count($db_mail).":{$db_name}\n",3,"/data/www/storage/siteo/storage/logs/nishi_log.txt");

						//指定時間経過したメールアドレスを取得
						if( count($db_mail) > 0 ){
							//smtp取得
							list($options['host_ip'], $options['port']) = Utility::getSmtpHost('setting');

							//配列からメルマガIDを取り出す
							foreach($db_mail as $lines){
								//HTMLメールフラグ(デフォルトはテキストメール)
								$mail_html_flg	= false;
								$body			= $outer_lines->body;

								//HTMLメールなら
								if( !empty($db_melmaga->html_body) ){
									$mail_html_flg	= true;
									$body			= $outer_lines->html_body;
								}

								//%変換設定文字列
								$convert_from_name = config('const.convert_mail_from_name');
								$convert_from_mail = config('const.convert_from_mail');

								//変換後の文字列を取得
								list($body, $subject, $from_name, $from_mail) = Utility::getMailConvertData($body, $outer_lines->title, $convert_from_name, $convert_from_mail, $db_name);
	//error_log("{$body}, {$subject}, {$from_name}, {$from_mail}\n",3,"/data/www/jray/storage/logs/nishi_log.txt");

								//%変換設定で設定されていないときデフォルト設定
								if( $from_name == $convert_from_name ){
									$from_name = config($db_name.'.const.mail_from_name');
								}

								//%変換設定で設定されていないときデフォルト設定
								if( $from_mail == $convert_from_mail ){
									$from_mail = config($db_name.'.const.replay_to_mail');;						
								}

								//送信元情報設定
								$options = [
									'html_flg'	 => $mail_html_flg,
									'from'		 => $from_mail,
									'from_name'	 => $from_name,
									'subject'	 => $subject,
									'template'	 => $db_name.'.'.config('const.admin_edit_mail'),
								];

								//送信データ設定
								$data = [
									'contents'		=> $body,
								];

								$err_flg = Utility::checkNgWordEmail($lines->mail, $db_name);

								//禁止ワードが含まれていたら
								if( !is_null($err_flg) ){
									continue;
								}

								$options['client_id'] = $lines->client_id;

								$options['subject'] = Utility::getConvertData($options['subject'], $db_name);
								$data['contents'] = Utility::getConvertData($data['contents'], $db_name);

								$db_obj = new User;
								$db_obj->setConnection($db_name);
								$db_user_data = $db_obj->where('id', $options['client_id'])->first();
								if( !empty($db_user_data) ){
									$data['contents'] = preg_replace("/\-%login_id\-/", $db_user_data->login_id, $data['contents']);
									$data['contents'] = preg_replace("/\-%password\-/", $db_user_data->password_raw, $data['contents']);
									$data['contents'] = preg_replace("/\-%token\-/", $db_user_data->remember_token, $data['contents']);
									$data['contents'] = preg_replace("/\-%accessKey\-/", $db_user_data->remember_token, $data['contents']);
								}

								Mail::to($lines->mail)->queue( new SendMail($options, $data) );

								//送信後、削除
								$delete = DB::connection($db_name)->delete("delete from registered_mail_queues where send_id = ".$outer_lines->id." and client_id = '{$lines->client_id}'");
							}
						}
					}

					//121分経過したメールアドレスを取得
					$db_mail = DB::connection($db_name)->select("select * from registered_mail_queues where now() >= (created_at + interval 121 minute)");

					//登録後送信メールの条件に当てはまらないで残っているメールアドレスを削除
					if( count($db_mail) > 0 ){
						foreach($db_mail as $lines){
							$delete = DB::connection($db_name)->delete("delete from registered_mail_queues where client_id = '{$lines->client_id}'");
						}
					}

				//登録後送信メールがなければregistered_mail_queuesテーブルに登録されているデータを削除
				}else{
					$db_client_id = DB::connection($db_name)->select("select * from registered_mail_queues limit 1");
//error_log(print_r($db_client_id,true)."\n",3,"/data/www/jray/storage/logs/nishi_log.txt");

					//registered_mail_queuesテーブルにデータがあれば
					if( count($db_client_id) > 0 ){
						$delete = DB::connection($db_name)->delete("delete from registered_mail_queues");
					}
				}
			}
		}
    }
}
