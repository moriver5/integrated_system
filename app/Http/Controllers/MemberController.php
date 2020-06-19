<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\KeibaRequest;
use App\Libs\SysLog;
use App\Libs\ClientLog;
use App\Model\User;
use App\Model\Contact;
use App\Model\Content;
use App\Model\Banner;
use App\Model\Top_content;
use App\Model\Top_product;
use App\Model\Payment_log;
use App\Model\Forecast;
use App\Model\Voice;
use App\Model\Visitor_log;
use App\Model\Magnification_setting;
use App\Model\Melmaga_log;
use App\Model\Melmaga_history_log;
use App\Model\Point_setting;
use App\Model\Point_log;
use App\Model\Check_chg_email;
use App\Model\Mail_content;
use App\Model\Grant_point;
use App\Model\User_info;
use Utility;
use Mail;
use Auth;
use Carbon\Carbon;
use App\Mail\SendMail;
use DB;
use PDO;
use Session;

class MemberController extends Controller
{
	protected $log_obj;
	protected $pv_log_obj;
	protected $list_hit_db_data;
	protected $list_hit_data;
	protected $list_banner_data;
	protected $login_bonus_pt;
	protected $login_bonus_msg;
	protected $login_bonus_disptime;
	protected $userinfo;
	
	public function __construct(Request $request)
	{
		//アクセスログ
		$this->log_obj			 = new SysLog(config('const.client_history_log_name'), config('const.client_log_dir_path').config('const.client_member_history_file_name'));

		//PV用ログ
		$this->pv_log_obj		 = new ClientLog();

		//バナーデータを取得
		$this->list_banner_data	 = Utility::getBanner();

		//ログインボーナス取得
		list($this->login_bonus_pt, $this->login_bonus_msg, $this->login_bonus_disptime) = Utility::getLoginBonusInfo();
		
		$this->userinfo = Utility::getUserInfo();
	}

	/**
	 * ログイン後のトップ画面表示
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request, $login_bonus_flg = null)
	{
		try{
			//会員ページのデフォルトのパラメータを取得
			$disp_param = Utility::getDefaultDispParam();

			//ログ出力
			$this->log_obj->addLog(config('const.display_list')['mem_top'].",{$disp_param['login_id']}");

			//PV出力
			$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_top']);

			$now_date = Carbon::now();

			//商品データ取得
			$list_product_data = Utility::getProduct($now_date, $disp_param);
			$list_sort_product_data = [];
			foreach($list_product_data as $lines){
				$list_sort_product_data[$lines['id']][] = $lines;
			}
			$list_new_product_data = [];
			foreach($list_sort_product_data as $id => $lines){
				if( count($lines) > 1 ){
					$discount;
					$min_data;
					foreach ($lines as $key => $sort_lines) {
						if( $key == 0 ){
							$discount = $sort_lines['discount'];
						}
						if( $discount >= $sort_lines['discount'] ){
							$min_data = $sort_lines;
						}
					}
					$list_new_product_data[] = $min_data;
				}else{
					$list_new_product_data[] = $lines[0];
				}
			}

			//予想データ取得
			$db_forecast_data = Forecast::query()
				->leftJoin('top_contents', 'top_contents.id', '=', 'forecasts.campaigns')
				->where('forecasts.open_flg', 1)
				->where('forecasts.product_id', 0)
				->select('forecasts.*', 'top_contents.img')
				->where('forecasts.disp_sdate', '<=', $now_date)
				->where('forecasts.disp_edate', '>=', $now_date)
				->orderBy('forecasts.category', 'desc')
				->get();

			$list_forecast_data = [];
			if( !empty($db_forecast_data) ){
				foreach($db_forecast_data as $lines){
					$listGroups = explode(",", $lines->groups);
					if( in_array($disp_param['group_id'], $listGroups) || empty($lines->groups) ){
						$list_forecast_data[] = $lines;
					}
				}
			}

			//ニュース記事取得
			$db_melmaga_data = Melmaga_log::query()
				->join('melmaga_history_logs', 'melmaga_logs.id', '=', 'melmaga_history_logs.melmaga_id')
				->where('melmaga_history_logs.client_id', $disp_param['client_id'])
				->where('newspage_flg', 1)
				->orderBy('melmaga_logs.send_date' , 'desc')
				->limit(config('const.disp_news_data_limit'))
				->get();

			$listMelmaga = [];
			if( !empty($db_melmaga_data) ){
				foreach($db_melmaga_data as $lines){
					//受信日時をフォーマット
					list($date1,$date2) = explode(" ", $lines->send_date);
					list($year, $mon, $day) = explode("-", $date1);
					list($hour, $min, $sec) = explode(":", $date2);
					$lines->send_date = Carbon::create($year, $mon, $day)->formatLocalized('%Y/%m/%d (%a)');
					$lines->from_name = Utility::getConvertData($lines->from_name);
					$lines->subject = Utility::getConvertData($lines->subject);
					$lines->text_body = Utility::getConvertData($lines->text_body);
					$lines->html_body = Utility::getConvertData($lines->html_body);
				}
				$listMelmaga = $db_melmaga_data;
			}

			//公開バナー、リンクする、キャンペーン→レギュラーの順番でソート
			$db_top_content_data = Top_content::where('sort_start_date', '<=', preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00')
				->where('sort_end_date', '>=', preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00')
				->where([
					'type'		=> 2,
					'open_flg'	=> 1,
					'link_flg'	=> 0
				])
				->where('img', '!=', '')
				->orderBy('type','asc')
				->orderBy('order_num','asc')->get();

			$list_top_content_data = [];
			if( count($db_top_content_data) > 0 ){
				foreach($db_top_content_data as $lines){
					//表示グループを取得
					$listGroups = explode(",", $lines->groups);

					//ユーザーがグループに所属している場合
					if( empty($listGroups[0]) || in_array($disp_param['group_id'], $listGroups) ){
						$list_top_content_data[$lines->id] = $lines->img;
					}
				}
			}

			//的中データを取得
			list($this->list_hit_db_data, $this->list_hit_data) = Utility::getHitAchievements($request, 'all');

			//画面表示パラメータ設定
			$disp_data = array_merge([
				'login_bonus_flg'		=> $login_bonus_flg,
				'login_bonus_pt'		=> $this->login_bonus_pt,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'userinfo'				=> $this->userinfo,
				'title'					=> config('const.list_title')['mem_top'],
				'list_news_data'		=> $listMelmaga,
				'list_banner'			=> $this->list_banner_data,
				'list_hit_data'			=> $this->list_hit_data,
				'mailbox_url_url'		=> config('const.base_url').'/'.config('const.mailbox_history_url_path'),
				'campaign_url'			=> config('const.base_url').'/'.config('const.campaign_url_path'),
				'regular_url'			=> config('const.base_url').'/'.config('const.regular_url_path'),
				'img_url'				=> config('const.base_url').'/'.config('const.images_path'),
				'top_content_img_url'	=> config('const.base_url').'/'.config('const.top_content_images_path'),
				'list_product_data'		=> $list_new_product_data,
				'list_forecast_data'	=> $list_forecast_data,
				'list_top_content_data'	=> $list_top_content_data
			],$disp_param);

			//画面表示
			return view('member.home', $disp_data);
		}catch(\Exception $e){
//			report($e);

			//例外エラー用
			return view('member.error');			
		}
	}

	/**
	 * ログイン後の情報公開(商品一覧と予想一覧)
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function expectationList()
	{
		try{
			//会員ページのデフォルトのパラメータを取得
			$disp_param = Utility::getDefaultDispParam();

			$now_date = Carbon::now();

			//現在参加中の購入リスト取得
			$list_buy_history = Utility::getBuyProductHistory($disp_param, 7);

			//商品データ取得
			$list_product_data = Utility::getProduct($now_date, $disp_param);
			$list_sort_product_data = [];
			foreach($list_product_data as $lines){
				$list_sort_product_data[$lines['id']][] = $lines;
			}
			$list_new_product_data = [];
			foreach($list_sort_product_data as $id => $lines){
				if( count($lines) > 1 ){
					$discount;
					$min_data;
					foreach ($lines as $key => $sort_lines) {
						if( $key == 0 ){
							$discount = $sort_lines['discount'];
						}
						if( $discount >= $sort_lines['discount'] ){
							$min_data = $sort_lines;
						}
					}
					$list_new_product_data[] = $min_data;
				}else{
					$list_new_product_data[] = $lines[0];
				}
			}

			//予想データ取得
			$db_forecast_data = Forecast::query()
				->leftJoin('top_contents', 'top_contents.id', '=', 'forecasts.campaigns')
				->where('forecasts.open_flg', 1)
				->where('forecasts.product_id', 0)
				->select('forecasts.*', 'top_contents.img')
				->where('forecasts.disp_sdate', '<=', $now_date)
				->where('forecasts.disp_edate', '>=', $now_date)
				->orderBy('forecasts.category', 'desc')
				->get();

			$list_forecast_data = [];
			if( !empty($db_forecast_data) ){
				foreach($db_forecast_data as $lines){
					$listGroups = explode(",", $lines->groups);
					if( in_array($disp_param['group_id'], $listGroups) || empty($lines->groups) ){
						$list_forecast_data[] = $lines;
					}
				}
			}

			$disp_data = array_merge([
				'title'					=> config('const.list_title')['mem_expectation_list'],
				'list_banner'			=> $this->list_banner_data,
				'login_bonus_flg'		=> false,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'login_bonus_pt'		=> $this->login_bonus_pt,
				'userinfo'				=> $this->userinfo,
				'list_forecast_data'	=> $list_forecast_data,
				'list_product_data'		=> $list_new_product_data,
				'list_buy_history_data' => $list_buy_history,
			],$disp_param);

			//ログ出力
			$this->log_obj->addLog(config('const.display_list')['mem_expectation_list'].",{$disp_param['login_id']}");

			//PV出力
			$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_expectation_list']);

			//画面表示
			return view('member.expectation_list', $disp_data);
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}

	/**
	 * 商品詳細画面
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function detailProduct($product_id)
	{
		try{
			//会員ページのデフォルトのパラメータを取得
			$disp_data = Utility::getDefaultDispParam();

			//現在時刻取得
			$now_date = Carbon::now();

			//商品取得
			$list_product_data = Utility::getProduct($now_date, $disp_data, $product_id);
			$list_sort_product_data = [];
			foreach($list_product_data as $lines){
				$list_sort_product_data[$lines['id']][] = $lines;
			}
			$list_new_product_data = [];
			foreach($list_sort_product_data as $id => $lines){
				if( count($lines) > 1 ){
					$discount;
					$min_data;
					foreach ($lines as $key => $sort_lines) {
						if( $key == 0 ){
							$discount = $sort_lines['discount'];
						}
						if( $discount >= $sort_lines['discount'] ){
							$min_data = $sort_lines;
						}
					}
					$list_new_product_data[] = $min_data;
				}else{
					$list_new_product_data[] = $lines[0];
				}
			}

			//%変換リスト取得
			$list_convert_data = Utility::getListConvertKeyValue();

			//ログ出力
			$this->log_obj->addLog(config('const.display_list')['mem_expectation_detail'].",{$disp_data['login_id']}");

			//PV出力
			$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_expectation_detail']);

			$disp_data = array_merge([
				'title'					=> config('const.list_title')['mem_expectation_detail'],
				'list_banner'			=> $this->list_banner_data,
				'login_bonus_flg'		=> false,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'login_bonus_pt'		=> $this->login_bonus_pt,
				'userinfo'				=> $this->userinfo,
				'product_detail'		=> $list_new_product_data,
				'convert_data'			=> $list_convert_data
			],$disp_data);

			//画面表示
			return view('member.product_detail', $disp_data);
		}catch(\Exception $e){
			report($e);
			//例外エラー用
			return view('member.error');			
		}
	}

	/**
	 * 
	 * 厳選情報を表示(排他制御あり)
	 * @return \Illuminate\Http\Response
	 */
	public function viewExpectation($category, $id)
	{
		try{
			//会員ページのデフォルトのパラメータを取得
			$disp_data = Utility::getDefaultDispParam();

			try {
				$dbh = DB::connection('mysql')->getPdo();
				$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);			//エラーの場合、例外を投げる設定
				$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);		//結果の行を連想配列で取得
				$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);					//SQLインジェクション対策
//				throw new \PDOException("テスト例外エラー");
			} catch (\PDOException $e) {
				$disp_data = array_merge([
					'title'					=> config('const.list_title')['mem_error_toll'],
					'list_banner'			=> $this->list_banner_data,
					'login_bonus_pt'		=> $this->login_bonus_pt,
					'login_bonus_flg'		=> false,
					'login_bonus_msg'		=> $this->login_bonus_msg,
					'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
					'userinfo'				=> $this->userinfo,

				],$disp_data);

				//例外エラーメッセージ表示
				return view('member.error_expectation', $disp_data);
			}

			//トランザクション開始
			$dbh->beginTransaction();

			$stmt = $dbh->prepare("select * from users where login_id = :login_id for update");
			$stmt->bindValue(":login_id", $disp_data['login_id']);
			$stmt->execute();

			$disp_data = [];
			while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
				$disp_data =  [
					'login_id'		=> $row['login_id'],
					'client_id'		=> $row['id'],
					'point'			=> $row['point'],
					'tel'			=> $row['credit_certify_phone_no'],
					'email'			=> $row['mail_address'],
					'group_id'		=> $row['group_id'],
					'ad_cd'			=> $row['ad_cd'],
					'status'		=> $row['status'],
					'pay_count'		=> $row['pay_count'],
					'password_raw'	=> $row['password_raw'],
					'token'			=> $row['remember_token'],
				];
			}

			//現在時刻取得
			$now_date = Carbon::now();

			//公開情報データ取得
			$db_data = Forecast::where('forecasts.open_flg', 1)
				->leftJoin('top_contents', 'top_contents.id', '=', 'forecasts.campaigns')
				->select('forecasts.*')
				->where('forecasts.id', $id)
				->where('forecasts.disp_sdate', '<=', $now_date)
				->where('forecasts.disp_edate', '>=', $now_date)
				->first();

			$list_db_data = [];
			$pt_subtraction_flg = false;
			if( !empty($db_data) ){
				//表示グループを取得
				$listGroups = explode(",", $db_data->groups);

				//ユーザーがグループに所属している場合
				if( in_array($disp_data['group_id'], $listGroups) || empty($listGroups[0]) ){
					//閲覧済のデータか確認するためvisitor_logsテーブルからデータ取得
					$stmt = $dbh->prepare("select * from visitor_logs where forecast_id = :forecast_id and client_id = :client_id");
					$stmt->bindValue(":forecast_id", (int)$id, PDO::PARAM_INT);
					$stmt->bindValue(":client_id", (int)$disp_data['client_id'], PDO::PARAM_INT);
					$stmt->execute();

					$visitor_data = [];
					while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
						$visitor_data[] = $row;
					}

					//未閲覧なら
					if( empty($visitor_data) ){
						//購入履歴データ取得
						$db_buy_data = DB::table('payment_logs')
								->join('top_products', 'top_products.id', '=', 'payment_logs.product_id')
								->select('payment_logs.product_id')
								->where('payment_logs.login_id', $disp_data['login_id'])
								->whereIn('payment_logs.status', ['0', '3'])
								->whereIn('payment_logs.product_id', explode(",", $db_data->product_id))->first();

						//購入履歴がない場合
						if( empty($db_buy_data) ){
							//ポイントが足りていればポイント減算
							if( ($disp_data['point'] - $db_data->point) >= 0 ){

								try{
									//ポイント減算
//									throw new \Exception("テスト例外エラー");
									$stmt = $dbh->prepare("update users set point = point - :point where id = :id");
									$stmt->bindValue(":point", (int)$db_data->point, PDO::PARAM_INT);
									$stmt->bindValue(":id", $disp_data['client_id']);
									$stmt->execute();
									$pt_subtraction_flg = true;

									//ポイントログ履歴更新
									$log = new Point_log([
										'login_id'					=> $disp_data['login_id'],
										'add_point'					=> $db_data->point,
										'prev_point'				=> $disp_data['point'],
										'current_point'				=> $disp_data['point'] - $db_data->point,
										'operator'					=> 'user'
									]);

									//データをinsert
									$log->save();
									
								//例外エラー
								}catch(\Exception $exception){
									report($exception);
									$dbh->rollback();

									$disp_data = array_merge([
										'title'					=> config('const.list_title')['mem_error_toll'],
										'list_banner'			=> $this->list_banner_data,
										'login_bonus_pt'		=> $this->login_bonus_pt,
										'login_bonus_flg'		=> false,
										'login_bonus_msg'		=> $this->login_bonus_msg,
										'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
										'userinfo'				=> $this->userinfo,

									],$disp_data);

									//例外エラーメッセージ表示
									return view('member.error_expectation', $disp_data);
								}

							//ポイントが足りてなかったら商品購入ページへ促すページを表示
							}else{
								$dbh->commit();

								//商品購入を促すページ表示
								return redirect()->to(config('const.member_settlement_path').'/'.$db_data->product_id);
							}
						}
					}

					$db_data->detail = Utility::escapeJsTag($db_data->detail);
					$list_db_data = $db_data;

				//ユーザーが所属していない場合
				}else{
					$dbh->commit();

					//商品購入を促すページ表示
					return redirect()->to(config('const.member_settlement_path').'/'.$db_data->product_id);
				}
			}

			//visitor_logsテーブルにアクセス履歴を登録
//			throw new \Exception("テスト例外エラー");
			$stmt = $dbh->prepare("insert ignore into visitor_logs(forecast_id,client_id,category,created_at,updated_at) values(:forecast_id, :client_id, :category, :created_at, :updated_at)");
			$stmt->bindValue(":forecast_id", (int)$id, PDO::PARAM_INT);
			$stmt->bindValue(":client_id", (int)$disp_data['client_id'], PDO::PARAM_INT);
			$stmt->bindValue(":category", (int)$category, PDO::PARAM_INT);
			$stmt->bindValue(":created_at", $now_date);
			$stmt->bindValue(":updated_at", $now_date);
			$stmt->execute();

			//visitor_logsテーブルからいま表示されている情報のアクセス数を取得
			$stmt = $dbh->prepare("select count(forecast_id) as count from visitor_logs where forecast_id = :forecast_id group by forecast_id");
			$stmt->bindValue(":forecast_id", $id);
			$stmt->execute();

			$db_data = [];
			while ( $row = $stmt->fetch(PDO::FETCH_ASSOC) ){
				$db_data[] = $row;
			}

			//visitor_logsテーブルに閲覧履歴がないときのデフォルト
			$view_count = 1;

			//visitor_logsテーブルに閲覧履歴があれば
			if( !empty($db_data) ){
				$view_count = $db_data[0]['count'];
			}

			//forecastsテーブルのvisitor(アクセス数)を更新
			$stmt = $dbh->prepare("update forecasts set visitor = :visitor where id = :id");
			$stmt->bindValue(":visitor", (int)$view_count, PDO::PARAM_INT);
			$stmt->bindValue(":id", $id);
			$stmt->execute();

			$dbh->commit();

			$disp_data = array_merge([
				'category'				=> $category,
				'title'					=> config('const.list_title')['mem_expectation_toll_detail'],
				'list_banner'			=> $this->list_banner_data,
				'melmaga_id'			=> $id,
				'login_bonus_pt'		=> $this->login_bonus_pt,
				'login_bonus_flg'		=> false,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'userinfo'				=> $this->userinfo,
				'db_data'				=> $list_db_data
			],$disp_data);

			//無料
			if( $category == 1 ){
				//ログ出力
				$this->log_obj->addLog(config('const.display_list')['mem_expectation_view'].",{$disp_data['login_id']}");

				//PV出力
				$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_expectation_view']);

			//有料
			}elseif( $category == 2 ){
				//ログ出力
				$this->log_obj->addLog(config('const.display_list')['mem_expectation_toll_view'].",{$disp_data['login_id']}");

				//PV出力
				$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_expectation_toll_view']);				
			}

			//画面表示
			return view('member.view_expectaction', $disp_data);

		} catch (\Exception $e) {
			$dbh->rollback();

			$disp_data = array_merge([
				'title'					=> config('const.list_title')['mem_error_toll'],
				'list_banner'			=> $this->list_banner_data,
				'login_bonus_pt'		=> $this->login_bonus_pt,
				'login_bonus_flg'		=> false,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'userinfo'				=> $this->userinfo,

			],$disp_data);

			//例外エラーメッセージ表示
			return view('member.error_expectation', $disp_data);
		}
	}

	/**
	 * 厳選情報を表示
	 * 以前のプログラム(排他制御なし)
	 * @return \Illuminate\Http\Response
	 */
	public function viewExpectation2($category, $id, $login_id)
	{
		//会員ページのデフォルトのパラメータを取得
//		$disp_param = Utility::getDefaultDispParam();
		$user = User::where("login_id", $login_id)->first();
		$disp_param =  [
			'login_id'		=> $user->login_id,
			'client_id'		=> $user->id,
			'point'			=> $user->point,
			'tel'			=> $user->credit_certify_phone_no,
			'email'			=> $user->mail_address,
			'group_id'		=> $user->group_id,
			'ad_cd'			=> $user->ad_cd,
			'status'		=> $user->status,
			'pay_count'		=> $user->pay_count,
			'password_raw'	=> $user->password_raw,
			'token'			=> $user->remember_token,
			'userinfo'		=> $this->userinfo,
			'login_bonus_flg'=> false,
			'login_bonus_msg'		=> $this->login_bonus_msg,
			'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
		];

		//デフォルトパラメータを画面パラメータの変数に設定
		$disp_data = $disp_param;

		//現在時刻取得
		$now_date = Carbon::now();

		//公開情報データ取得
		$db_data = Forecast::where('open_flg', 1)
			->where('id', $id)
			->where('disp_sdate', '<=', $now_date)
			->where('disp_edate', '>=', $now_date)
			->first();

		$list_db_data = [];
		$pt_subtraction_flg = false;
		if( !empty($db_data) ){
			//表示グループを取得
			$listGroups = explode(",", $db_data->groups);

			//ユーザーがグループに所属している場合
			if( in_array($disp_param['group_id'], $listGroups) || empty($listGroups) ){
				//閲覧済のデータか確認するためvisitor_logsテーブルからデータ取得
				$visitor_data = Visitor_log::where('forecast_id', $id)->where('client_id', $disp_param['client_id'])->first();

				//未閲覧なら
				if( empty($visitor_data) ){
					//購入履歴データ取得
					$db_buy_data = DB::table('payment_logs')
							->join('top_products', 'top_products.id', '=', 'payment_logs.product_id')
							->select('payment_logs.product_id')
							->where('payment_logs.login_id', $disp_param['login_id'])
							->whereIn('payment_logs.status', ['0', '3'])
							->whereIn('payment_logs.product_id', explode(",", $db_data->campaigns))->first();

					//購入履歴がない場合
					if( empty($db_buy_data) ){
						//ポイントが足りていればポイント減算
						if( ($disp_param['point'] - $db_data->point) >= 0 ){
							try{
								//ポイント減算
								DB::transaction(function() use($disp_param, $db_data, &$pt_subtraction_flg){
//									throw new \Exception("テスト例外エラー");
									User::where('id',$disp_param['client_id'])->increment('point', '-'.$db_data->point);
									$pt_subtraction_flg = true;

									//ポイントログ履歴更新
									$log = new Point_log([
										'login_id'					=> $disp_param['login_id'],
										'add_point'					=> $db_data->point,
										'prev_point'				=> $disp_param['point'],
										'current_point'				=> $disp_param['point'] - $db_data->point,
										'operator'					=> 'user'
									]);

									//データをinsert
									$log->save();
								});

							//例外エラー
							}catch(\Exception $exception){
								$disp_data = array_merge([
									'title'			=> config('const.list_title')['mem_error_toll'],
									'list_banner'	=> $this->list_banner_data,
									'list_hit_data'	=> $this->list_hit_data
								],$disp_data);

								//例外エラーメッセージ表示
								return view('member.error_expectation', $disp_data);
							}

						//ポイントが足りてなかったら商品購入ページへ促すページを表示
						}else{
							$disp_data = array_merge([
								'title'			=> config('const.list_title')['mem_warning_toll'],
								'list_banner'	=> $this->list_banner_data,
								'list_hit_data'	=> $this->list_hit_data,
								'use_point'		=> $db_data->point
							],$disp_data);

							//PV出力
							$this->pv_log_obj->addPvLogDb();

							//商品購入を促すページ表示
							return view('member.warning_expectation', $disp_data);
						}
					}
				}

				$db_data->detail = Utility::escapeJsTag($db_data->detail);
				$list_db_data = $db_data;

			//ユーザーが所属していない場合
			}else{
				//PV出力
				$this->pv_log_obj->addPvLogDb();

				$disp_data = array_merge([
					'title'			=> config('const.list_title')['mem_warning_toll'],
					'list_banner'	=> $this->list_banner_data,
					'use_point'		=> $db_data->point
				],$disp_data);

				//商品購入を促すページ表示
				return view('member.warning_expectation', $disp_data);
			}
		}

		//visitor_logsテーブルからいま表示されている情報のアクセス数を取得
		$db_data = DB::select("select count(forecast_id) as count from visitor_logs where forecast_id = {$id} group by forecast_id");

		//visitor_logsテーブルに閲覧履歴がないときのデフォルト
		$view_count = 1;

		//visitor_logsテーブルに閲覧履歴があれば
		if( !empty($db_data) ){
			$view_count = $db_data[0]->count;
		}

		try{
			//forecastsテーブルのvisitor(アクセス数)を更新
			DB::transaction(function() use($id, $view_count){
				Forecast::where('id', $id)->update(['visitor' => $view_count]);
			});
		}catch(\Exception $e){
		}

		//visitor_logsテーブルにアクセス履歴を登録
		try{
			DB::transaction(function() use($disp_param, $now_date, $category, $id){
//				throw new \Exception("テスト例外エラー");
				$result = DB::insert("insert ignore into visitor_logs(forecast_id,client_id,category,created_at,updated_at) values("
				. $id.","
				. $disp_param['client_id'].","
				. $category.","
				. "'".$now_date."',"
				. "'".$now_date."');");
			});

		}catch(\Exception $e){
			//ポイント減算されていたら
			if( $pt_subtraction_flg === true ){
				try{
					//減算されたポイントを加算
					DB::transaction(function() use($disp_param, $list_db_data){
						User::where('id',$disp_param['client_id'])->increment('point', $list_db_data->point);
					});

				//例外エラー
				}catch(\Exception $exception){
				}

				$disp_data = array_merge([
					'title'			=> config('const.list_title')['mem_error_toll']
				],$disp_data);

				//例外エラーメッセージ表示
				return view('member.error_expectation', $disp_data);
			}
		}

		$disp_data = array_merge([
			'title'			=> config('const.list_title')['mem_expectation_toll_detail'],
			'list_banner'	=> $this->list_banner_data,
			'melmaga_id'	=> $id,
			'login_bonus_pt'=> $this->login_bonus_pt,
			'db_data'		=> $list_db_data
		],$disp_data);

		//ログ出力
		$this->log_obj->addLog(config('const.display_list')['mem_expectation'].",{$disp_param['login_id']}");

		//PV出力
		$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_expectation']);

		//画面表示
		return view('member.view_expectaction', $disp_data);
	}

	/**
	 * ログイン後の的中実績画面表示
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function hit(Request $request)
	{
		try{
			//会員ページのデフォルトのパラメータを取得
			$disp_param = Utility::getDefaultDispParam();

			//ログ出力
			$this->log_obj->addLog(config('const.display_list')['mem_hit'].",{$disp_param['login_id']}");

			//PV出力
			$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_hit']);

			//的中データを取得
			list($this->list_hit_db_data, $this->list_hit_data) = Utility::getHitAchievements($request);

			$disp_data = array_merge([
				'title'					=> config('const.list_title')['mem_hit'],
				'list_banner'			=> $this->list_banner_data,
				'pager'					=> $this->list_hit_db_data,
				'list_hit_data'			=> $this->list_hit_data,
				'login_bonus_pt'		=> $this->login_bonus_pt,
				'login_bonus_flg'		=> false,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'userinfo'				=> $this->userinfo,
			], $disp_param);

			//画面表示
			return view('member.hit', $disp_data);
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}
	
	/**
	 * ログイン後の喜びの声画面表示
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function voice()
	{
		try{
			//会員ページのデフォルトのパラメータを取得
			$disp_param = Utility::getDefaultDispParam();

			//ログ出力
			$this->log_obj->addLog(config('const.display_list')['mem_voice'].",{$disp_param['login_id']}");

			//PV出力
			$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_voice']);

			//画面に表示する件数
			$disp_limit = config('const.disp_achievements_limit');

			//お客様の声データ取得(日ごとにランダム取得)
			//inRandomOrderの引数指定でその引数で固定取得となるからページャーがあっても固定取得となる
//			$db_data = Voice::where('open_flg', 1)->inRandomOrder(Carbon::today()->timestamp)->paginate($disp_limit);
			$db_data = Voice::where('open_flg', 1)->orderBy('post_date', 'desc')->paginate($disp_limit);

			$listData = [];
			if( !empty($db_data) ){
				setlocale(LC_ALL, 'ja_JP.UTF-8');
				foreach($db_data as $lines){
					//投稿日時
					list($year, $mon, $day) = explode("-", $lines->post_date);

					//タイトル
					$title = $lines->title;
					if( empty($lines->title) ){
						$title = config('const.none_post_title');
					}

					//投稿者
					$name = $lines->name;
					if( empty($lines->name) ){
						$name = config('const.none_post_name');
					}

					//画面表示変数
					$listData[] = [
						'post_date' => Carbon::create($year, $mon, $day)->formatLocalized('%Y年%m月%d日 (%a)'),
						'title' => $title,
						'name' => $name,
						'msg' => $lines->msg,
						'img' => $lines->img
					];
				}
			}

			$disp_data = array_merge([
				'title'					=> config('const.list_title')['mem_voice'],
				'list_banner'			=> $this->list_banner_data,
				'login_bonus_pt'		=> $this->login_bonus_pt,
				'login_bonus_flg'		=> false,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'userinfo'				=> $this->userinfo,
				'page_link'				=> $db_data->links('vendor.pagination.user_default'),	//ページャーのテンプレートを指定
				'db_data'				=> $listData,
				'ver'					=> time()
			], $disp_param);

			//画面表示
			return view('member.voice', $disp_data);
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}
	
	/**
	 * ログイン後のよくある質問画面表示
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function qa()
	{
		try{
			//会員ページのデフォルトのパラメータを取得
			$disp_param = Utility::getDefaultDispParam();

			//ログ出力
			$this->log_obj->addLog(config('const.display_list')['mem_qa'].",{$disp_param['login_id']}");

			//PV出力
			$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_qa']);

			//特定商取引に基づく表記のコンテンツを取得
			$db_data = Content::where('id', 4)->first();

			$disp_data = array_merge([
				'title'					=> config('const.list_title')['mem_qa'],
				'contents'				=> Utility::getConvertData($db_data->contents),
				'list_banner'			=> $this->list_banner_data,
				'login_bonus_pt'		=> $this->login_bonus_pt,
				'login_bonus_flg'		=> false,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'userinfo'				=> $this->userinfo,
			],$disp_param);

			//画面表示
			return view('member.qa', $disp_data);
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}
	
	/**
	 * ログイン後のお問い合わせ画面表示
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function info()
	{
		try{
			//会員ページのデフォルトのパラメータを取得
			$disp_param = Utility::getDefaultDispParam();

			//画面表示用配列にデフォルトのパラメータを追加
			$disp_data = array_merge([
				'title'					=> config('const.list_title')['mem_info'],
				'list_banner'			=> $this->list_banner_data,
				'login_bonus_pt'		=> $this->login_bonus_pt,
				'login_bonus_flg'		=> false,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'userinfo'				=> $this->userinfo,
			],$disp_param);

			//PV出力
			$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_info']);

			//画面表示
			return view('member.info', $disp_data);
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}
	
	/**
	 * ログイン後のよくある質問から質問内容を送信
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function infoConfirm(Request $request)
	{
		try{
			$this->validate($request, [
//				'subject'	 => 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.subject_length'),
				'contents'	 => 'bail|required|surrogate_pair_check|emoji_check|max:'.config('const.contents_length'),
			]);

			//会員ページのデフォルトのパラメータを取得
			$disp_param = Utility::getDefaultDispParam();

			//件名
//			$subject = $request->input('subject');

			//お問い合わせ内容取得
			$msg = $request->input('contents');

			//お問い合わせ内容が空なら
			if( empty($msg) ){
				//画面表示用配列
				$disp_msg = [
					'inquiry_msg'			=> __('messages.inquiry_nonmsg'),
					'title'					=> config('const.list_title')['mem_info'],
					'list_banner'			=> $this->list_banner_data,
					'login_bonus_pt'		=> $this->login_bonus_pt,
					'login_bonus_flg'		=> false,
					'login_bonus_msg'		=> $this->login_bonus_msg,
					'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
					'userinfo'				=> $this->userinfo,
				];

				//画面表示用配列にデフォルトのパラメータを追加
				$disp_msg = array_merge($disp_msg,$disp_param);

				//画面表示
				return view('member.info', $disp_msg);
			}

			$save_data = [
				'subject'	=> config('const.user_mail_subject'),
				'msg'		=> $msg,
				'client_id'	=> $disp_param['client_id'],
				'email'		=> $disp_param['email'],
				'status'	=> $disp_param['status']
			];

			//グループID
			if( !empty($disp_param['group_id']) ){
				$save_data['group_id'] = $disp_param['group_id'];
			}

			//広告コード
			if( !empty($disp_param['ad_cd']) ){
				$save_data['memo'] = $disp_param['ad_cd'];
			}

			$contact = new Contact($save_data);

			//お問い合わせ内容をDB保存
			$contact->save();

			//ログ出力
			$this->log_obj->addLog(config('const.display_list')['mem_info_send'].",{$disp_param['login_id']}");

			//PV出力
			$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_info_send']);

			//画面表示
			return redirect()->to(config('const.member_info_path').'/'.config('const.info_end_status'));
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}

	/**
	 * ログイン後のよくある質問から質問内容を送信完了後の完了画面を表示
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function infoSendEnd(Request $request)
	{
		try{
			//会員ページのデフォルトのパラメータを取得
			$disp_param = Utility::getDefaultDispParam();

			//画面表示用配列にデフォルトのパラメータを追加
			$disp_data = array_merge([
				'title'					=> config('const.list_title')['mem_info'],
				'list_banner'			=> $this->list_banner_data,
				'login_bonus_pt'		=> $this->login_bonus_pt,
				'login_bonus_flg'		=> false,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'userinfo'				=> $this->userinfo,
			],$disp_param);

			return view('member.info_send', $disp_data);
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}

	/**
	 * ログイン後の商品購入画面表示
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function settlement($product_id = null)
	{
		try{
			//会員ページのデフォルトのパラメータを取得
			$disp_param = Utility::getDefaultDispParam();

			//ログ出力
			$this->log_obj->addLog(config('const.display_list')['mem_buy_list'].",{$disp_param['login_id']}");

			$add_product = '';
			if( !is_null($product_id) ){
				$add_product = '-商品ID：'.$product_id;
			}

			//PV出力
			$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_buy_list'].$add_product);

			//現在時刻取得
			$now_date = Carbon::now();

			//商品リスト取得
			$list_product_data = Utility::getProduct($now_date, $disp_param, $product_id);
			$list_sort_product_data = [];
			foreach($list_product_data as $lines){
				$list_sort_product_data[$lines['id']][] = $lines;
			}
			$list_new_product_data = [];
			foreach($list_sort_product_data as $id => $lines){
				if( count($lines) > 1 ){
					$discount;
					$min_data;
					foreach ($lines as $key => $sort_lines) {
						if( $key == 0 ){
							$discount = $sort_lines['discount'];
						}
						if( $discount >= $sort_lines['discount'] ){
							$min_data = $sort_lines;
						}
					}
					$list_new_product_data[] = $min_data;
				}else{
					$list_new_product_data[] = $lines[0];
				}
			}

			//販売ポイントを取得
			$db_pt_data = Utility::getSalePoint($now_date);

			$checked = '';
			if( !is_null($product_id) ){
				$checked = 'checked';
			}

			//画面表示用変数
			$disp_data = array_merge([
				'checked'				=> $checked,
				'title'					=> config('const.list_title')['mem_settlement'],
				'list_banner'			=> $this->list_banner_data,
				'login_bonus_pt'		=> $this->login_bonus_pt,
				'login_bonus_flg'		=> false,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'db_pt_data'			=> $db_pt_data,
				'userinfo'				=> $this->userinfo,
				'db_data'				=> $list_new_product_data
			],$disp_param);

			//画面表示
			return view('member.settlement', $disp_data);
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}

	/**
	 * ログイン後の商品購入完了画面
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function settlement_done($order_id = null)
	{
		try{
			//会員ページのデフォルトのパラメータを取得
			$disp_param = Utility::getDefaultDispParam();

			//ログ出力
			$this->log_obj->addLog(config('const.display_list')['mem_buy_end'].",{$disp_param['login_id']}");

			//PV出力
			$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_buy_end']);

			//現在時刻取得
			$now_date = Carbon::now();

			if( !empty($order_id) ){
				$db_data = Payment_log::where('order_id', $order_id)->first();
				if( !empty($db_data) ){
					//入金ｴﾗｰ
					if( $db_data->status == config('const.settlement_result')[2] || 
						$db_data->status == config('const.settlement_result')[6] ){
						//メンバートップへ遷移
						return redirect(config('const.member_top_path'));
					}
				}
			}

			//画面表示用変数
			$disp_data = array_merge([
				'title'					=> config('const.list_title')['mem_settlement'],
				'list_banner'			=> $this->list_banner_data,
				'login_bonus_pt'		=> $this->login_bonus_pt,
				'login_bonus_flg'		=> false,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'userinfo'				=> $this->userinfo,
			],$disp_param);

			//画面表示
			return view('member.settlement_done', $disp_data);
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}
	
	/**
	 * ログアウト処理
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function logout()
	{
		//会員ページのデフォルトのパラメータを取得
		$disp_param = Utility::getDefaultDispParam();

		//ログ出力
		$this->log_obj->addLog(config('const.display_list')['logout'].",{$disp_param['login_id']}");

		//PV出力
		$this->pv_log_obj->addPvLogDb(config('const.display_list')['logout']);

		$this->guard()->logout();

		//会員登録前のトップページへリダイレクト
		return redirect(property_exists($this, 'redirectAfterLogout') ? $this->redirectAfterLogout : config('const.nonmember_top_path'));
	}
	
	protected function guard()
	{
		return Auth::guard('user');
	}
	
	/**
	 * ログイン後の会員情報変更
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function userSetting()
	{
		try{
			//認証情報取得
			$user = \Auth::user();

			//PCメールアドレスが登録済
			if( !empty($user->mail_address) ){
				$pc_mail_status = __('messages.email_registed');
			}else{
				$pc_mail_status = __('messages.email_unregisted');			
			}

			//メッセージ用変数
			$disp_msg = [
				'email'						=> $user['mail_address'],
				'pc_email_status_msg'		=> $pc_mail_status,
			];

			//会員ページのデフォルトのパラメータを取得
			$disp_param = Utility::getDefaultDispParam();

			//画面表示用配列にデフォルトのパラメータを追加
			$disp_data = array_merge([
				'title'					=> config('const.list_title')['mem_setting'],
				'list_banner'			=> $this->list_banner_data,
				'list_domain'			=> config('const.disp_mobile_domain'),
				'password_raw'			=> $disp_param['password_raw'],
				'login_bonus_flg'		=> false,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'userinfo'				=> $this->userinfo,
			],$disp_msg, $disp_param);

			//PV出力
			$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_setting']);

			//画面表示
			return view('member.user_setting', $disp_data);
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}

	/**
	 * 会員情報変更-パスワード変更
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function settingUpdatePassword(KeibaRequest $request)
	{
		try{
			//パスワードのエラーチェック
			//パスワードの未入力
			$this->validate($request, [
				'new_password'				 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|confirmed|is_space',
				'new_password_confirmation'	 => 'required|use_char_check|max:'.config('const.password_max_length').'|min:'.config('const.password_length').'|is_space',
			]);

			//会員ページのデフォルトのパラメータを取得
			$disp_param = Utility::getDefaultDispParam();

			try{
				//パスワードをアップデート
				DB::transaction(function() use($disp_param, $request){
					$update = User::where('login_id', $disp_param['login_id'])
						->update([
							'password'				 => bcrypt($request->new_password),
							'password_raw'			 => $request->new_password,
							'last_access_datetime'	 => Carbon::now()
						]);
				});
			} catch (\Exception $e) {
			}

			list($host_ip, $port) = Utility::getSmtpHost('setting');

			//送信元情報設定
			$options = [
				'client_id'	 => $disp_param['client_id'],
				'host_ip'	 => $host_ip,
				'port'		 => $port,
				'from'		 => config('const.mail_from'),
				'from_name'	 => config('const.mail_from_name'),
				'subject'	 => config('const.mail_info_subject'),
				'template'	 => config('const.list_site_const')[$_SERVER['SERVER_NAME']].'.'.'emails.password_change',
			];

			//送信データ設定
			$data = [
				'login_id'		=> $disp_param['login_id'],
				'password'		=> $request->new_password,
				'top_url'		=> config('const.base_url'),
				'member_url'	=> config('const.base_url').config('const.member_top_path').'/'.$disp_param['token']
			];

			//メール送信
			Mail::to($disp_param['email'])->queue( new SendMail($options, $data) );

			//ログ出力
			$this->log_obj->addLog(config('const.display_list')['mem_setting_pass'].",{$disp_param['login_id']}");

			//PV出力
			$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_setting_pass']);

			//画面表示
			return redirect(config('const.member_setting_end_path').'/'.config('const.setting_end_type_password'));
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}

	/**
	 * 会員情報変更-メールアドレス変更
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function settingUpdateEmail(KeibaRequest $request)
	{
		try{
			//認証情報取得
			$user = \Auth::user();

			//PCメールアドレス
			$pc_email = mb_strtolower(trim($request->input('pc_email')));

			//PCメールアドレスが空なら
			if( empty($pc_email) ){
				//PCメールアドレスが登録済
				if( !empty($user->mail_address) ){
					$pc_mail_status = __('messages.email_registed');
				}else{
					$pc_mail_status = __('messages.email_unregisted');			
				}

				//メッセージ用変数
				$disp_msg = [
					'pc_email_status_msg'		=> $pc_mail_status
				];

				//会員ページのデフォルトのパラメータを取得
				$disp_param = Utility::getDefaultDispParam();

				$disp_msg = array_merge($disp_msg, $disp_param);

				//会員情報変更画面を表示
				return redirect(config('const.member_setting_end_path'));
			}

			$check_param = [];

			//PCメールアドレスが入力されていたら
			if( !empty($pc_email) ){
				//PCメールアドレスの形式チェック
				$check_param = ['pc_email' => 'bail|email|max:'.config('const.email_length').'|unique:'.Session::get('operation_select_db').'.users,mail_address,'.$user->login_id.',login_id|check_mx_domain'];
			}

			//エラーチェック
			$this->validate($request, $check_param);

			$err_flg = Utility::checkNgWordEmail($pc_email);

			//メールアドレスに禁止ワードが含まれていたらトップへリダイレクト
			if( !is_null($err_flg) ){
				return redirect(config('const.member_setting_path'));
			}

			//ログインID
			$login_id = $user->login_id;

			//PCメール送信
			if( !empty($pc_email) ){
				$db_data = Check_chg_email::where('email', $pc_email)->first();
				if( empty($db_data) ){
					try{
						$token = bin2hex(openssl_random_pseudo_bytes(16));

						//メールアドレス変更確認用のテーブルにデータ登録
						DB::transaction(function() use($login_id, $pc_email, $token){
							DB::insert("insert ignore into check_chg_emails("
								. "login_id, "
								. "token, "
								. "email, "
								. "created_at, "
								. "updated_at) "
								. "values("
								. "'".$login_id."', "
								. "'".$token."', "
								. "'".$pc_email."', "
								. "'".Carbon::now()."', "
								. "'".Carbon::now()."') "
								. "on duplicate key update "
								. "token = '{$token}',"
								. "email = '{$pc_email}',"
								. "updated_at = '".Carbon::now()."';");
						});
					}catch(\Exception $e){
					}
				}else{
					$token = $db_data->token;
				}

				//自動メールのデータ取得
				$db_cnt = Mail_content::where('id', 8)->first();

				//データがあれば
				if( !empty($db_cnt) ){
					//%変換設定では変換できない文字列の処理
					//メールアドレス変更確認用URL
					$body = $db_cnt->body;
					$body = preg_replace("/\-%mail_chg_url\-/", config('const.base_url').config('const.member_setting_email_chg_path').'/'.$login_id.'/'.$token, $body);

					//変換後の文字列を取得
					list($body, $subject, $from_name, $from_mail) = Utility::getMailConvertData($body, $db_cnt->subject, $db_cnt->from, $db_cnt->from_mail);

					list($host_ip, $port) = Utility::getSmtpHost('setting');

					//送信元情報設定
					$options = [
						'client_id'	 => $user->id,
						'host_ip'	 => $host_ip,
						'port'		 => $port,
						'from'		 => $from_mail,
						'from_name'	 => $from_name,
						'subject'	 => $subject,
						'template'	 => config('const.list_site_const')[$_SERVER['SERVER_NAME']].'.'.config('const.email_change'),
					];

					//送信データ設定
					$data = [
						'contents'		=> $body,
					];

					//メールアドレス変更先へメール送信
					Mail::to($pc_email)->queue( new SendMail($options, $data) );
				}
/*
				list($host_ip, $port) = Utility::getSmtpHost('setting');

				//送信元情報設定
				$options = [
					'host_ip'	 => $host_ip,
					'port'		 => $port,
					'from'		 => config('const.mail_from'),
					'from_name'	 => config('const.mail_from_name'),
					'subject'	 => config('const.mail_info_subject'),
					'template'	 => config('const.list_site_const')[$_SERVER['SERVER_NAME']].'.'.config('const.email_change'),
				];

				//送信データ設定
				$data = [
					'mail_chg_url'	=> config('const.base_url').config('const.member_setting_email_chg_path').'/'.$login_id.'/'.$token,
				];

				Mail::to($pc_email)->queue( new SendMail($options, $data) );
*/
			}

			//ログ出力
			$this->log_obj->addLog(config('const.display_list')['mem_setting_mail'].",{$login_id}");

			//PV出力
			$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_setting_mail']);

			//画面表示
			return redirect(config('const.member_setting_end_path').'/'.config('const.setting_check_email'));
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}

	/**
	 * 会員情報変更-パスワード変更完了・メールアドレス変更確認のメッセージ表示
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function settingUpdateEnd($type = null)
	{
		try{
			//認証情報取得
			$user = \Auth::user();

			//PCメールアドレスが登録済
			if( !empty($user->mail_address) ){
				$pc_mail_status = __('messages.email_registed');
			}else{
				$pc_mail_status = __('messages.email_unregisted');			
			}

			//メッセージ用変数
			$disp_msg = [
				'pc_email_status_msg'		=> $pc_mail_status
			];

			//メールアドレス用 変更確認メール送信メッセージ
			if( $type == config('const.setting_check_email') ){
				$disp_msg = array_merge($disp_msg, ['mail_send_msg' => __('messages.email_check_change')]);

			//メールアドレス用 変更完了メッセージ
			}elseif( $type == config('const.setting_end_type_email') ){
				$disp_msg = array_merge($disp_msg, ['mail_send_msg' => __('messages.email_change')]);

				//PV出力
				$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_setting_end']);

			//パスワード用メッセージ
			}elseif( $type == config('const.setting_end_type_password') ){
				$disp_msg = array_merge($disp_msg, ['send_msg' => __('messages.password_change')]);

				//PV出力
				$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_setting_end']);
			}

			//会員ページのデフォルトのパラメータを取得
			$disp_param = Utility::getDefaultDispParam();

			//画面表示用配列にデフォルトのパラメータを追加
			$disp_data = array_merge([
				'title'					=> config('const.list_title')['mem_setting_end'],
				'list_banner'			=> $this->list_banner_data,
				'login_bonus_pt'		=> $this->login_bonus_pt,
				'login_bonus_flg'		=> false,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'userinfo'				=> $this->userinfo,
			],$disp_msg, $disp_param);

			//パスワード変更完了
			if( $type == config('const.setting_end_type_password') ){
				return view('member.setting_pw_done', $disp_data);

			//メールアドレス変更確認
			}elseif( $type == config('const.setting_check_email') ){
				return view('member.setting_mail_check', $disp_data);

			//メールアドレス変更完了
			}elseif( $type == config('const.setting_end_type_email') ){
				return view('member.setting_mail_done', $disp_data);
			}
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}

	/*
	 * メールアドレス変更確認のメールの中に記載されているURLをクリックすると呼び出される
	 * usersテーブルのメールアドレスの変更処理後、リダイレクト
	 */
	function clickUpdateEmailLink($login_id, $token)
	{
		try{
			//キーがあれば
			if( !empty($token) ){
				$db_data = Check_chg_email::where('token', $token)->first();

				//メールアドレス変更用のメールリンクからクリックされたのかキーで確認
				if( !empty($db_data) ){
					try{
						DB::transaction(function() use($db_data, $login_id){
							User::where('login_id', $login_id)->update([
								'mail_address' => $db_data->email
							]);
						});
					}catch(\Exception $e){
					}

					$delete = Check_chg_email::where('token', $token)->delete();
				}
			}

			//完了画面へリダイレクト
			return redirect(config('const.member_setting_end_path').'/'.config('const.setting_end_type_email'));
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}

	/**
	 * ログイン後のプライバシーポリシー
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function privacy()
	{
		try{
			//会員ページのデフォルトのパラメータを取得
			$disp_param = Utility::getDefaultDispParam();

			//ログ出力
			$this->log_obj->addLog(config('const.display_list')['mem_privacy'].",{$disp_param['login_id']}");

			//PV出力
			$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_privacy']);

			//プライバシーポリシーのコンテンツを取得
			$db_data = Content::where('id', 7)->first();

			$disp_data = array_merge([
				'title'					=> config('const.list_title')['mem_privacy'],
				'contents'				=> Utility::getConvertData($db_data->contents),
				'list_banner'			=> $this->list_banner_data,
				'login_bonus_pt'		=> $this->login_bonus_pt,
				'login_bonus_flg'		=> false,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'userinfo'				=> $this->userinfo,
			],$disp_param);

			//画面表示
			return view('member.privacy', $disp_data);
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}
	
	/**
	 * ログイン後の利用規約
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function rule()
	{
		try{
			//会員ページのデフォルトのパラメータを取得
			$disp_param = Utility::getDefaultDispParam();

			//ログ出力
			$this->log_obj->addLog(config('const.display_list')['mem_rule'].",{$disp_param['login_id']}");

			//PV出力
			$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_rule']);

			//利用規約のコンテンツを取得
			$db_data = Content::where('id', 6)->first();

			$disp_data = array_merge([
				'title'					=> config('const.list_title')['mem_rule'],
				'contents'				=> Utility::getConvertData($db_data->contents),
				'list_banner'			=> $this->list_banner_data,
				'login_bonus_pt'		=> $this->login_bonus_pt,
				'login_bonus_flg'		=> false,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'userinfo'				=> $this->userinfo,
			],$disp_param);

			//画面表示
			return view('member.rule',$disp_data);
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}
	
	/**
	 * ログイン後の特定商取引
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function outline()
	{
		try{
			//会員ページのデフォルトのパラメータを取得
			$disp_param = Utility::getDefaultDispParam();

			//ログ出力
			$this->log_obj->addLog(config('const.display_list')['outline'].",{$disp_param['login_id']}");

			//PV出力
			$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_outline']);

			//特定商取引のコンテンツを取得
			$db_data = Content::where('id', 8)->first();

			$disp_data = array_merge([
				'title'					=> config('const.list_title')['mem_outline'],
				'contents'				=> Utility::getConvertData($db_data->contents),
				'list_banner'			=> $this->list_banner_data,
				'login_bonus_pt'		=> $this->login_bonus_pt,
				'login_bonus_flg'		=> false,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'userinfo'				=> $this->userinfo,
			],$disp_param);

			//画面表示
			return view('member.outline', $disp_data);
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}

	/**
	 * ログイン後の特定商取引法に基づく表記
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function legal()
	{
		try{
			//会員ページのデフォルトのパラメータを取得
			$disp_param = Utility::getDefaultDispParam();

			//ログ出力
			$this->log_obj->addLog(config('const.display_list')['mem_legal'].",{$disp_param['login_id']}");

			//PV出力
			$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_legal']);

			//特記事項のコンテンツを取得
			$db_data = Content::where('id', 18)->first();

			$disp_data = array_merge([
				'title'					=> config('const.list_title')['mem_outline'],
				'contents'				=> Utility::getConvertData($db_data->contents),
				'list_banner'			=> $this->list_banner_data,
				'login_bonus_pt'		=> $this->login_bonus_pt,
				'login_bonus_flg'		=> false,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'userinfo'				=> $this->userinfo,
			],$disp_param);

			//画面表示
			return view('member.legal', $disp_data);
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}

	/**
	 * ログイン後の簡単ご利用ガイド
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function guide()
	{
		try{
			//会員ページのデフォルトのパラメータを取得
			$disp_param = Utility::getDefaultDispParam();

			//ログ出力
			$this->log_obj->addLog(config('const.display_list')['mem_guide'].",{$disp_param['login_id']}");

			//PV出力
			$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_guide']);

			//ご利用ガイドのコンテンツを取得
			$db_data = Content::where('id', 19)->first();

			$disp_data = array_merge([
				'title'					=> config('const.list_title')['mem_guide'],
				'contents'				=> Utility::getConvertData($db_data->contents),
				'list_banner'			=> $this->list_banner_data,
				'login_bonus_pt'		=> $this->login_bonus_pt,
				'login_bonus_flg'		=> false,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'userinfo'				=> $this->userinfo,
			],$disp_param);

			//画面表示
			return view('member.guide', $disp_data);
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}

	/**
	 * 会員のMAILBOX画面を表示
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function mailbox()
	{
		try{
			//会員ページのデフォルトのパラメータを取得
			$disp_param = Utility::getDefaultDispParam();

			//メルマガログ取得
			$db_melmaga = Melmaga_log::query()
				->join('melmaga_history_logs', 'melmaga_logs.id', '=', 'melmaga_history_logs.melmaga_id')
				->where('melmaga_history_logs.client_id', $disp_param['client_id'])
				->orderBy('melmaga_history_logs.sort_date' , 'desc')
				->paginate(config('const.disp_mailbox_limit'));

			$disp_data = [];
			if( !empty($db_melmaga) ){
				foreach($db_melmaga as $lines){
					//受信日時をフォーマット
					list($date1,$date2) = explode(" ", $lines->send_date);
					list($year, $mon, $day) = explode("-", $date1);
					list($hour, $min, $sec) = explode(":", $date2);
					$lines->send_date = sprintf("%d/%d/%d %02d:%02d", $year, $mon, $day, $hour, $min);
					$lines->from_name = Utility::getConvertData($lines->from_name);
					$lines->subject = Utility::getConvertData($lines->subject);
					$lines->text_body = Utility::getConvertData($lines->text_body);
					$lines->html_body = Utility::getConvertData($lines->html_body);
				}
				$disp_data['db_data'] = $db_melmaga;
			}

			//お問い合わせログ取得
			//運営側からの送信のみ取得
			$db_info = Contact::query()->where('client_id', $disp_param['client_id'])
				->whereNull ('created_at')
				->orderBy('reply_date', 'desc')
				->paginate(config('const.disp_mailbox_limit'));
			$disp_data['db_info'] = $db_info;

			if( $db_melmaga->lastPage() > $db_info->lastPage() ){
				$disp_data['links'] = $db_melmaga->links('vendor.pagination.user_default');
			}else{
				$disp_data['links'] = $db_info->links('vendor.pagination.user_default');		
			}

			$disp_data = array_merge([
				'title'					=> config('const.list_title')['mem_mailbox'],
				'list_banner'			=> $this->list_banner_data,
				'login_bonus_pt'		=> $this->login_bonus_pt,
				'login_bonus_flg'		=> false,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'userinfo'				=> $this->userinfo,
			],$disp_data, $disp_param);

			//ログ出力
			$this->log_obj->addLog(config('const.display_list')['mem_mailbox'].",{$disp_param['login_id']}");

			//PV出力
			$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_mailbox']);

			//画面表示
			return view('member.mailbox', $disp_data);
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}

	/**
	 * 会員のMAILBOX一覧から個別のメルマガ画面を表示
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function viewHistoryMelmaga($melmaga_id)
	{
		try{
			//会員ページのデフォルトのパラメータを取得
			$disp_param = Utility::getDefaultDispParam();

			//メルマガログ取得
			$db_data = Melmaga_log::query()
				->join('melmaga_history_logs', 'melmaga_logs.id', '=', 'melmaga_history_logs.melmaga_id')
				->where('melmaga_id', $melmaga_id)
				->where('client_id', $disp_param['client_id'])
				->first();

			$disp_data = [];
			if( !empty($db_data) ){
				$db_data->from_name	= Utility::getConvertData($db_data->from_name);
				$db_data->subject	= Utility::getConvertData($db_data->subject);
				$db_data->text_body = Utility::getConvertData($db_data->text_body);
				$db_data->html_body = Utility::getConvertData($db_data->html_body);
				$db_data->text_body = Utility::getConvertLink($db_data->text_body);
				$db_data->html_body = Utility::getConvertLink($db_data->html_body);

				$db_data->text_body = preg_replace("/\-%login_id\-/", $disp_param['login_id'], $db_data->text_body);
				$db_data->html_body = preg_replace("/\-%login_id\-/", $disp_param['login_id'], $db_data->html_body);

				$db_data->text_body = preg_replace("/\-%password\-/", $disp_param['password_raw'], $db_data->text_body);
				$db_data->html_body = preg_replace("/\-%password\-/", $disp_param['password_raw'], $db_data->html_body);

				$db_data->text_body = preg_replace("/".config('const.melmaga_id')."/", $melmaga_id, $db_data->text_body);
				$db_data->html_body = preg_replace("/".config('const.melmaga_id')."/", $melmaga_id, $db_data->html_body);

				$db_data->text_body = preg_replace("/".config('const.access_key')."/", $disp_param['token'], $db_data->text_body);
				$db_data->html_body = preg_replace("/".config('const.access_key')."/", $disp_param['token'], $db_data->html_body);
				$disp_data['db_data'] = $db_data;

				//未読なら既読にする
				if( empty($db_data->read_flg) ){
					$update = Melmaga_history_log::where('client_id', $db_data->client_id)->where('melmaga_id', $db_data->melmaga_id)->update(['read_flg' => 1]);
				}
			}

			$disp_data = array_merge([
				'title'					=> config('const.list_title')['mem_mailbox'],
				'list_banner'			=> $this->list_banner_data,
				'login_bonus_pt'		=> $this->login_bonus_pt,
				'login_bonus_flg'		=> false,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'userinfo'				=> $this->userinfo,
			],$disp_data, $disp_param);

			//ログ出力
			$this->log_obj->addLog(config('const.display_list')['mem_mailbox_melmaga'].",{$disp_param['login_id']}");

			//PV出力
			$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_mailbox_melmaga']);

			//画面表示
			return view('member.mailbox_view_melmaga', $disp_data);
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}

	/**
	 * 会員のMAILBOX一覧から個別のお問い合わせ画面を表示
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function viewHistoryInfo($info_id)
	{
		try{
			//会員ページのデフォルトのパラメータを取得
			$disp_param = Utility::getDefaultDispParam();

			//お問い合わせログ取得
			$db_data = Contact::query()
				->where('id', $info_id)
				->first();

			$disp_data = [];
			if( !empty($db_data) ){
				$db_data->msg = Utility::getConvertData($db_data->msg);
				$db_data->msg = Utility::getConvertLink($db_data->msg);

				$db_data->msg = preg_replace("/\-%login_id\-/", $disp_param['login_id'], $db_data->msg);
				$db_data->msg = preg_replace("/\-%password\-/", $disp_param['password_raw'], $db_data->msg);
				$db_data->msg = preg_replace("/".config('const.access_key')."/", $disp_param['token'], $db_data->msg);

				$disp_data['db_data'] = $db_data;

				//未読なら既読にする
				if( empty($db_data->read_flg) ){
					$update = Contact::where('client_id', $db_data->client_id)->where('id', $info_id)->update(['read_flg' => 1]);
				}
			}

			$disp_data = array_merge([
				'title'					=> config('const.list_title')['mem_mailbox'],
				'list_banner'			=> $this->list_banner_data,
				'login_bonus_pt'		=> $this->login_bonus_pt,
				'login_bonus_flg'		=> false,
				'login_bonus_msg'		=> $this->login_bonus_msg,
				'login_bonus_disptime'	=> $this->login_bonus_disptime * 1000,
				'userinfo'				=> $this->userinfo,
			],$disp_data, $disp_param);

			//ログ出力
			$this->log_obj->addLog(config('const.display_list')['mem_mailbox_info'].",{$disp_param['login_id']}");

			//PV出力
			$this->pv_log_obj->addPvLogDb(config('const.display_list')['mem_mailbox_info']);

			//画面表示
			return view('member.mailbox_view_info', $disp_data);
		}catch(\Exception $e){
			//例外エラー用
			return view('member.error');			
		}
	}
}
