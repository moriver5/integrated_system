<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Model\User;
use App\Model\Melmaga_log;
use App\Model\Confirm_email;
use App\Mail\SendMail;
use Mail;
use Carbon\Carbon;
use Utility;
use DB;

class MelmagaReserveDelivery extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'melmaga:reserve';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'メルマガの予約配信用コマンド';

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

		//現在時刻をyyyymmddhhmmにフォーマット
		$sort_date = preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00';

		//サイトごとのDB名リストを取得
		$listSiteDb = DB::select("select db from operation_dbs");
//error_log("test1\n",3,"/data/www/storage/siteo/storage/logs/nishi_log.txt");
		if( count($listSiteDb) > 0 ){
			foreach($listSiteDb as $lines){
				$db_name = $lines->db;
//error_log("test2\n",3,"/data/www/storage/siteo/storage/logs/nishi_log.txt");
				//配信予定日時を過ぎた配信待ちのデータ取得
				$db_data = DB::connection($db_name)->select("select * from melmaga_logs where sort_reserve_send_date <= {$sort_date} and send_status in(0,4) order by sort_reserve_send_date desc");
//error_log("test3\n",3,"/data/www/storage/siteo/storage/logs/nishi_log.txt");
/*
				$db_data = Melmaga_log::setConnection($db_name)->query()
					->where('sort_reserve_send_date', '<=', $sort_date)
					->whereIn('send_status', [0,4])
					->orderBy('sort_reserve_send_date', 'desc')
					->get();
 */
//error_log("test4\n",3,"/data/www/storage/siteo/storage/logs/nishi_log.txt");
				//配信予定日時を過ぎ、配信状況待ちのデータあれば
				if( !empty($db_data) ){
					//配列からメルマガIDを取り出す
					foreach($db_data as $lines){
						//HTMLメールフラグ(デフォルトはテキストメール)
						$mail_html_flg	= false;
						$body			= $lines->text_body;

						//HTMLメールなら
						if( !empty($db_melmaga->html_body) ){
							$mail_html_flg	= true;
							$body			= $lines->html_body;
						}

						//メルマガIDへ変換
						$body = preg_replace("/".config('const.melmaga_id')."/", $lines->id, $body);
//error_log("test5\n",3,"/data/www/storage/siteo/storage/logs/nishi_log.txt");
						//送信元情報設定
						$options = [
							'html_flg'	 => $mail_html_flg,
							'from'		 => Utility::getConvertData($lines->from_mail, $db_name),
							'from_name'	 => Utility::getConvertData($lines->from_name, $db_name),
							'subject'	 => Utility::getConvertData($lines->subject, $db_name),
							'template'	 => $db_name.'.'.config('const.admin_edit_mail'),
						];
//error_log("test6\n",3,"/data/www/storage/siteo/storage/logs/nishi_log.txt");
						//送信データ設定
						$data = [
							'contents'		=> Utility::getConvertData($body, $db_name),
						];
		//error_log("メルマガID：{$lines->id}\n",3,"/data/www/jray/storage/logs/nishi_log.txt");
//error_log("test7\n",3,"/data/www/storage/siteo/storage/logs/nishi_log.txt");
						//確認アドレス宛に送信するにチェックが入っていた場合
						if( empty($lines->send_status) ){
							//確認アドレス宛にメルマガ送信
							$db_obj = new  Confirm_email;
							$db_obj->setConnection($db_name);
							$db_email = $db_obj->get();
							if( count($db_email) > 0 ){
								foreach($db_email as $confirm){
									$err_flg = Utility::checkNgWordEmail($confirm->email, $db_name);

									//禁止ワードが含まれていたら
									if( !is_null($err_flg) ){
										continue;
									}

									Mail::to($confirm->email)->send( new SendMail($options, $data) );
								}
							}
						}
//error_log("step1\n",3,"/data/www/storage/siteo/storage/logs/nishi_log.txt");
						//別プロセスでメルマガIDごとに配信
						$process = new Process(config('const.artisan_command_path')." melmaga:reserve_send {$db_name} {$lines->id} {$lines->send_status} > /dev/null");

						//非同期実行(/data/www/jray/app/Console/Commands/MelmagaReserveSendDelivery.php)
						$process->start();

						//非同期実行の場合は別プロセスが実行する前に終了するのでsleepを入れる
						//1.5秒待機
						usleep(1500000);
					}
				}
			}
		}
    }
}
