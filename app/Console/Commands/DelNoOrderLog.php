<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Session;

class DelNoOrderLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment_logs:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '24時間経過しても注文にならない不要なデータを削除(SSL決済ページ遷移前に戻って決済方法を変えて注文し直したり、決済後にブラウザでバックするとpayment_logsに不要なデータが登録される)';

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
		//サイトごとのDB名リストを取得
		$listSiteDb = DB::select("select db from operation_dbs");

		if( count($listSiteDb) > 0 ){
			foreach($listSiteDb as $lines){
				$db_name = $lines->db;

				//24時間経過しても注文にならなかったゴミデータをpayment_logsテーブルから削除
				$delete = DB::connection($db_name)->delete("delete from payment_logs where status = '1' and sendid is null and now() >= (updated_at + interval ".config('const.paymentlogs_credit_expire_minute')." minute)");

				//クレジット決済(pay_type:2)で7日間経過してもstatusが1、sendidに値があるデータを削除
				//Axesから結果データが返ってくればstatusは1以外になるが7日間経過してもstatusが1のままはSSL決済ページで注文していないと判断
				//Axesから結果データを受け取れないサーバーエラーの可能性もありイレギュラーだが7日間の余裕を持たせる
				$delete = DB::connection($db_name)->delete("delete from payment_logs where status = '1' and pay_type = 2 and sendid != '' and now() >= (updated_at + interval ".config('const.paymentlogs_credit_expire_minute')." minute)");

				//ネットバンクの全国の金融機関から決済しようとして14日間経過しても注文にいたらない不要なデータを削除
				$delete = DB::connection($db_name)->delete("delete from payment_logs where status = '1' and pay_type = 3 and sendid != '' and now() >= (updated_at + interval ".config('const.paymentlogs_netbank_expire_minute')." minute)");
			}
		}
	}
}
