<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\User;
use App\Model\Melmaga_log;
use App\Model\Melmaga_temp_immediate_mail;
use App\Model\Confirm_email;
use App\Mail\SendMail;
use App\Model\Melmaga_history_log;
use Mail;
use Carbon\Carbon;
use Utility;

class MelmagaImmediateDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'melmaga:delivery {db_name} {melmaga_id} {send_status} {history_flg}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'メルマガの即時配信用コマンド';

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
		//DB名取得
		$db_name = $this->argument("db_name");

		//メルマガの件名、内容を取得
		$db_obj = new Melmaga_log;
		$db_obj->setConnection($db_name);
		$db_melmaga = $db_obj->where('id', $this->argument('melmaga_id'))->first();

		//HTMLメールフラグ(デフォルトはテキストメール)
		$mail_html_flg	= false;
		$body			= $db_melmaga->text_body;

		//HTMLメールなら
		if( !empty($db_melmaga->html_body) ){
			$mail_html_flg	= true;
			$body			= $db_melmaga->html_body;
		}

		//メルマガIDへ変換
		$body = preg_replace("/".config('const.melmaga_id')."/", $this->argument('melmaga_id'), $body);

		//送信元情報設定
		$options = [
			'add_header' => config('const.add_mail_header'),
			'html_flg'	 => $mail_html_flg,
			'from'		 => Utility::getConvertData($db_melmaga->from_mail, $db_name),
			'from_name'	 => Utility::getConvertData($db_melmaga->from_name, $db_name),
			'subject'	 => Utility::getConvertData($db_melmaga->subject, $db_name),
			'template'	 => $db_name.'.'.config('const.admin_edit_mail'),
		];

		//送信データ設定
		$data = [
			'contents'		=> Utility::getConvertData($body, $db_name),
		];

		//メルマガ配信IDを条件に配信先メールアドレスのリスト取得
		$db_obj = new User;
		$db_obj->setConnection($db_name);
		$db_data = $db_obj->join('melmaga_temp_immediate_mails', 'users.id', '=', 'melmaga_temp_immediate_mails.client_id')->where('melmaga_temp_immediate_mails.melmaga_id', $this->argument('melmaga_id'))->get();

		if( !empty($db_data) ){
			//確認アドレス宛に送信するにチェックが入っていた場合
			if( !empty($this->argument('history_flg')) ){
				//確認アドレス宛にメルマガ送信
				$db_obj = new  Confirm_email;
				$db_obj->setConnection($db_name);
				$db_email = $db_obj->get();
				if( count($db_email) > 0 ){
					foreach($db_email as $lines){
//error_log("確認アドレス：".$lines->email."\n",3,"/data/www/storage/siteo/storage/logs/nishi_log.txt");

						$err_flg = Utility::checkNgWordEmail($lines->email, $db_name);

						//禁止ワードが含まれていたら
						if( !is_null($err_flg) ){
							continue;
						}

						Mail::to($lines->email)->send( new SendMail($options, $data) );
					}
				}
			}

			$now_date = Carbon::now();

			//現在時刻をyyyymmddhhmmにフォーマット
			$sort_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';

			$db_obj = new Melmaga_log;
			$db_obj->setConnection($db_name);

			//履歴を残す(send_status:4以外)
			if( $this->argument('send_status') != 4 ){
				//メルマガ配信日時 配信状況：1(送信中)
				$update = $db_obj->where('id', $this->argument('melmaga_id'))
					->update([
						'send_date' => $now_date,
						'send_status' => 1]);

			//履歴を残さない
			}else{
				//メルマガ配信日時
				$update = $db_obj->where('id', $this->argument('melmaga_id'))
					->update(['send_date' => $now_date]);
			}

			//リレーサーバーを使用するとき
			if( $db_melmaga->send_method == 1 ){
				//smtp取得
				list($options['host_ip'], $options['port']) = Utility::getSmtpHost('melmaga');
				if( is_null($options['host_ip']) ){
					$db_obj = new Melmaga_log;
					$db_obj->setConnection($db_name);
					$db_melmaga = $db_obj->where('id', $this->argument('melmaga_id'))->update([
						'send_method' => null
					]);
				}
			}

			//配列から１つ取り出しメール送信
			foreach($db_data as $lines){
				//0.1秒待機
				usleep(100000);
//error_log("メルマガ：".$lines->mail_address."\n",3,"/data/www/jray/storage/logs/nishi_log.txt");
//error_log($this->argument('melmaga_id')." {$lines->id} {$sort_date} {$now_date}\n",3,"/data/www/storage/siteo/storage/logs/nishi_log.txt");

				$db_obj = new Melmaga_history_log([
					'melmaga_id'	=> $this->argument('melmaga_id'),
					'client_id'		=> $lines->id,
					'sort_date'		=> $sort_date,
					'created_at'	=> $now_date,
					'updated_at'	=> $now_date
				]);
				
				$db_obj->setConnection($db_name);

				//DB保存
				$db_obj->save();

				$err_flg = Utility::checkNgWordEmail($lines->mail_address, $db_name);

				//禁止ワードが含まれていたら
				if( !is_null($err_flg) ){
					continue;
				}

				$options['client_id'] = $lines->id;

				$contents['contents'] = $data['contents'];

				mb_regex_encoding("UTF-8");
				//usersテーブルのremember_tokenへ変換
				$contents['contents'] = preg_replace("/".config('const.access_key')."/u", $lines->remember_token, $contents['contents']);
				$contents['contents'] = preg_replace("/\-%login_id\-/u", $lines->login_id, $contents['contents']);
				$contents['contents'] = preg_replace("/\-%password\-/u", $lines->password_raw, $contents['contents']);
				$contents['contents'] = preg_replace("/\-%token\-/u", $lines->remember_token, $contents['contents']);
				$contents['contents'] = preg_replace("/\-%accessKey\-/u", $lines->remember_token, $contents['contents']);

				try{
					//メール送信
					Mail::to($lines->mail_address)->send( new SendMail($options, $contents) );

				}catch(\Exception $e){
					//ユーザーのDM購読を強制停止にする
					$db_user_obj = new User;
					$db_user_obj->setConnection($db_name);
					$db_user_obj->where('id', $lines->id)->update([
						'mail_status' => config('const.edit_dm_status')[2][0]
					]);
					continue;
				}

				//メール送信数をカウント
				$db_obj = new Melmaga_log;
				$db_obj->setConnection($db_name);
				$delete = $db_obj->where('id', $this->argument('melmaga_id'))->increment('send_count', 1);

				//メール送信後、リストから削除
				$db_obj = new Melmaga_temp_immediate_mail;
				$db_obj->setConnection($db_name);
				$delete = $db_obj->where('melmaga_id', $this->argument('melmaga_id'))->where('client_id', $lines->id)->delete();
			}

			$db_obj = new Melmaga_log;
			$db_obj->setConnection($db_name);

			//履歴を残す以外(send_status:4以外)
			if( $this->argument('send_status') != 4 ){
				//メルマガ配信状況の更新→配信済:send_status→1
				$update = $db_obj->where('id', $this->argument('melmaga_id'))->update(['send_status' => 2]);

			//履歴を残さない場合(send_status:4)
			}else{
				//メルマガ配信状況の更新→履歴を残さない場合の配信済:send_status→5
				$update = $db_obj->where('id', $this->argument('melmaga_id'))->update(['send_status' => 5]);				
			}
		}
    }
}
