<?php

namespace App\Libs;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Auth;
use App\Model\Convert_table;
use App\Model\Admin;
use App\Model\Agency;
use App\Model\User;
use App\Model\Achievement;
use App\Model\Voice;
use App\Model\Email_ng_word;
use App\Model\Relay_server;
use App\Model\Banner;
use App\Model\Grant_point;
use App\Model\User_info;
use App\Model\Payment_log;
use App\Model\Top_product;
use App\Model\Top_content;
use App\Model\Magnification_setting;
use App\Model\Point_setting;
use App\Model\Tipster;
use App\Model\Forecast;
use Carbon\Carbon;
use DB;

class Utility
{
	/*
	 * 予想師一覧を取得
	 */
	public function getTipster()
	{
		$db_data = Tipster::where('disp_flg', 1)->get();

		return $db_data;
	}

	/*
	 * 販売するポイントを取得
	 */
	public function getSalePoint($now_date)
	{
		//倍率設定済の購入ポイント取得
		$query = Magnification_setting::query();
		$query->join('point_settings', 'magnification_settings.category_id', '=', 'point_settings.category_id');
		$query->where('magnification_settings.start_date','<=', $now_date);
		$query->where('magnification_settings.end_date', '>=', $now_date);
		$db_pt_data = $query->get();

		//倍率設定がされていなければ通常設定のポイントを取得
		if( count($db_pt_data) == 0 ){
			//magnification_settingsテーブルの通常設定IDを取得
			$db_pt_data = Magnification_setting::first();
			if( !empty($db_pt_data) ){
				//通常設定の購入ポイントを取得
				$query = Point_setting::query();
				$db_pt_data = $query->where('category_id', $db_pt_data->default_id)->get();
			}
		}
		return $db_pt_data;
	}

	/*
	 * 現在参加中のキャンペーンのデータ初期化
	 */
	private function _getInitBuyProductHistory($db_data)
	{
		$db_data->product_id = !empty($db_data->product_id) ? $db_data->product_id:'';
		$db_data->groups	 = !empty($db_data->groups) ? $db_data->groups:'';
		$db_data->discount	 = !empty($db_data->discount) ? $db_data->discount:'';
		$db_data->title		 = !empty($db_data->title) ? $db_data->title:'';
		$db_data->money		 = !empty($db_data->money) ? $db_data->money:0;
		$db_data->title		 = !empty($db_data->title) ? $db_data->title:'';

		return $db_data;
	}

	/*
	 * 現在参加中のキャンペーンを取得
	 */
	public function getBuyProductHistory($user_param, $lastDays = 7)
	{
		$dt = new Carbon();
//		$one_week = $dt->subDay($lastDays);

		//購入履歴データ取得
		$db_buy_data = DB::table('payment_logs')
				->join('users', 'payment_logs.login_id', '=', 'users.login_id')
				->leftJoin('top_products', 'top_products.id', '=', 'payment_logs.product_id')
				->select('top_products.title as title','top_products.groups as groups','top_products.discount as discount','payment_logs.money as product_money','payment_logs.*')
				->where('users.id', $user_param['client_id'])
				->whereIn('payment_logs.status', [0,3])->get();
//				->where('payment_logs.updated_at', '>=', $one_week)->orderBy('payment_logs.order_id','desc')->get();

		$list_buy_history = [];
		if( count($db_buy_data) > 0 ){
			foreach($db_buy_data as $lines){
				//初期化
				$lines = $this->_getInitBuyProductHistory($lines);

				if( is_null($lines->product_id) || $lines->product_id == '' ){
					continue;
				}

				//期間終了日時前のデータを取得
				$db_forecast_data = DB::table('forecasts')
					->select('forecasts.id', 'forecasts.disp_sdate', 'forecasts.disp_edate')
					->join('top_products', 'top_products.id', '=', 'forecasts.product_id')
					->where('forecasts.product_id',$lines->product_id)
					->where('forecasts.category',2)
					->where('forecasts.open_flg',1)
					->where('forecasts.disp_edate', '>=', $dt)
					->where('forecasts.open_edate', '>=', $dt)
	//				->where('forecasts.disp_sdate', '<=', $dt)
	//				->where('forecasts.open_sdate', '<=', $dt)
					->first();

				//データがなければスキップ
				if( empty($db_forecast_data) ){
					continue;
				}

				$is_active = false;
				$now_date = new Carbon();
				$disp_sdate = new Carbon(!empty($db_forecast_data->disp_sdate) ? $db_forecast_data->disp_sdate:date('Y-m-d'));
				$disp_edate = new Carbon(!empty($db_forecast_data->disp_edate) ? $db_forecast_data->disp_edate:date('Y-m-d'));

				//現在時刻が表示期間内なら
				if( $now_date->between($disp_sdate, $disp_edate) ){
					$is_active = true;
				}

				$listGroups = explode(",", $lines->groups);
				if( in_array($user_param['group_id'], $listGroups) || empty($lines->groups) ){
					$json_data = json_decode($lines->discount, true);
					if( !empty($json_data) ){
						foreach($json_data['json'] as $discouns){
							$discouns['groups']	 = !empty($discouns['groups']) ? $discouns['groups']:'';
							$discouns['hold_pt'] = !empty($discouns['hold_pt']) ? $discouns['hold_pt']:0;
							$discouns['title']	 = !empty($discouns['title']) ? $discouns['title']:'';
							$discouns['money']	 = !empty($discouns['money']) ? $discouns['money']:0;
							
							$listGroups = explode(",", $discouns['groups']);

							//保有ポイント＆所属ポイントが存在するとき
							if( !empty($discouns['hold_pt']) && !empty($discouns['groups']) ){
								if( in_array($user_param['group_id'], $listGroups) && $user_param['point'] >= $discouns['hold_pt'] ){
									$list_buy_history[] = [
										'forecast_id'	=> !empty($db_forecast_data->id) ? $db_forecast_data->id:'',
										'product_id'	=> $lines->product_id,
										'is_active'		=> $is_active,
										'title'			=> $lines->title,
										'sub_title'		=> $discouns['title'],
										'money'			=> $discouns['money'],
									];
								}

							//保有ポイントのみ存在するとき
							}elseif( !empty($discouns['hold_pt']) && empty($discouns['groups']) ){
								if( $user_param['point'] >= $discouns['hold_pt'] ){
									$list_buy_history[] = [
										'forecast_id'	=> !empty($db_forecast_data->id) ? $db_forecast_data->id:'',
										'product_id'	=> $lines->product_id,
										'is_active'		=> $is_active,
										'title'			=> $lines->title,
										'sub_title'		=> $discouns['title'],
										'money'			=> $discouns['money'],
									];
								}

							//所属グループのみ存在するとき
							}elseif( empty($discouns['hold_pt']) && !empty($discouns['groups']) ){
								if( in_array($user_param['group_id'], $listGroups) ){
									$list_buy_history[] = [
										'forecast_id'	=> !empty($db_forecast_data->id) ? $db_forecast_data->id:'',
										'product_id'	=> $lines->product_id,
										'is_active'		=> $is_active,
										'title'			=> $lines->title,
										'sub_title'		=> $discouns['title'],
										'money'			=> $discouns['money'],
									];
								}
							}else{
								$list_buy_history[] = [
									'forecast_id'	=> !empty($db_forecast_data->id) ? $db_forecast_data->id:'',
									'product_id'	=> $lines->product_id,
									'is_active'		=> $is_active,
									'title'			=> $lines->title,
									'sub_title'		=> '',
									'money'			=> $lines->money,
								];					
							}
						}
					}else{
						$list_buy_history[] = [
							'forecast_id'	=> !empty($db_forecast_data->id) ? $db_forecast_data->id:'',
							'product_id'	=> $lines->product_id,
							'is_active'		=> $is_active,
							'title'			=> $lines->title,
							'sub_title'		=> '',
							'money'			=> $lines->money,
						];
					}
				}
			}
		}
		return $list_buy_history;
	}

	/*
	 * 商品の初期設定
	 */
	private function _getInitProduct($db_data)
	{
		$db_data->id			 = !empty($db_data->id) ? $db_data->id:'';
		$db_data->tipster		 = !empty($db_data->tipster) ? $db_data->tipster:'';
		$db_data->tipster_id	 = !empty($db_data->tipster_id) ? $db_data->tipster_id:'';
		$db_data->comment		 = !empty($db_data->comment) ? $db_data->comment:'';
		$db_data->contents		 = !empty($db_data->contents) ? $db_data->contents:'';
		$db_data->title			 = !empty($db_data->title) ? $db_data->title:'';
		$db_data->saddle		 = !empty($db_data->saddle) ? $db_data->saddle:'';
		$db_data->tickets		 = !empty($db_data->tickets) ? $db_data->tickets:'';
		$db_data->quantity		 = !empty($db_data->quantity) ? $db_data->quantity:'';
		$db_data->point			 = !empty($db_data->point) ? $db_data->point:0;
		$db_data->money			 = !empty($db_data->money) ? $db_data->money:0;
		$db_data->groups		 = !empty($db_data->groups) ? $db_data->groups:'';
		$db_data->discount		 = !empty($db_data->discount) ? $db_data->discount:'';
		$db_data->sold_out_date	 = !empty($db_data->sold_out_date) ? $db_data->sold_out_date:date('y-m-d');

		return $db_data;
	}

	/*
	 * 商品を取得
	 */
	public function getProduct($now_date, $user_param, $product_id = null)
	{
		//payment_logsテーブルから決済済以外のproduct_idを取得
		$sub_query = Payment_log::query()
			->select('product_id')
			->where('login_id', $user_param['login_id'])
			->where('status', '=', config('const.settlement_result')[3])
			->distinct()
			->get();

		//1度購入した商品を省いた商品を取得
		$query = Top_product::query();

		//商品IDが指定されているとき条件に追加
		if( !is_null($product_id) ){
			if( is_array($product_id) ){
				$query->whereIn('top_products.id', $product_id);				
			}else{
				$query->where('top_products.id', $product_id);
			}
		}

		$db_data = $query->where('open_flg', 1)
			->join('tipsters', 'tipsters.id', '=', 'top_products.tipster')
			->leftJoin('payment_logs', 'payment_logs.product_id', '=', 'top_products.id')
			->select('top_products.*', 'tipsters.name as tipster', 'tipsters.id as tipster_id', 'tipsters.contents')
			->where('top_products.sort_start_date', '<=', preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00')
			->where('top_products.sort_end_date', '>=', preg_replace("/(\d{4})(\/|\-)(\d{2})(\/|\-)(\d{2})\s(\d{2}):(\d{2})(:\d{2})?/", "$1$3$5$6$7", $now_date).'00')
			->where('top_products.money', '>', 0)
			->whereNotIn('top_products.id', $sub_query)
			->orderBy('top_products.order_num', 'asc')
			->orderBy('top_products.id', 'asc')
			->distinct()
			->get();

		$now_date = new Carbon();

		$delay_count = 0;
		$list_product = [];
		if( count($db_data) > 0 ){
			foreach($db_data as $lines){
				$listGroups = explode(",", $lines->groups);

				//ユーザーがグループに所属していない場合
				if( !empty($listGroups[0]) > 0 && !in_array($user_param['group_id'], $listGroups) ){
					continue;
				}

				//初期設定
				$lines = $this->_getInitProduct($lines);

				$is_sold_out = false;
				$sold_out_date = new Carbon($lines->sold_out_date);

				//現在時刻が完了時刻を過ぎたら
				if( $now_date->gt($sold_out_date) ){
					$is_sold_out = true;
				}

				$delay_count++;
				if( $delay_count == 1 ){
					$delay = 0;
				}elseif( $delay_count == 2 ){
					$delay = 500;
				}elseif( $delay_count == 3 ){
					$delay = 800;
					$delay_count = 0;
				}

				$listSort = [];
				$json_data = json_decode($lines->discount, true);
				$exist_flg = false;
				if( isset($json_data['json']) ){
					foreach($json_data['json'] as $discouns){
						if( empty($discouns) ){
							continue;
						}
						$discouns['groups']		 = !empty($discouns['groups']) ? $discouns['groups']:'';
						$discouns['hold_pt']	 = !empty($discouns['hold_pt']) ? $discouns['hold_pt']:0;
						$discouns['group_id']	 = !empty($discouns['group_id']) ? $discouns['group_id']:'';
						$discouns['point']		 = !empty($discouns['point']) ? $discouns['point']:0;
						$discouns['title']		 = !empty($discouns['title']) ? $discouns['title']:'';
						$discouns['money']		 = !empty($discouns['money']) ? $discouns['money']:0;

						$listGroups = explode(",", $discouns['groups']);
						if( empty($discouns['money']) ){
							$discouns['money'] = $lines->money;
						}

						//保有ポイント＆所属ポイントが存在するとき
						if( !empty($discouns['hold_pt']) && !empty($discouns['groups']) ){
							if( in_array($user_param['group_id'], $listGroups) && $user_param['point'] >= $discouns['hold_pt'] ){
								$exist_flg = true;
								$list_product[] = [
									'delay'			=> $delay,
									'id'			=> $lines->id,
									'tipster'		=> $lines->tipster,
									'tipster_id'	=> $lines->tipster_id,
									'comment'		=> $lines->comment,
									'contents'		=> $lines->contents,
									'title'			=> $lines->title,
									'sub_title'		=> $discouns['title'],
									'saddle'		=> $lines->saddle,
									'tickets'		=> $lines->tickets,
									'quantity'		=> $lines->quantity,
									'point'			=> $lines->point,
									'money'			=> $lines->money,
									'discount'		=> $discouns['money'],
									'groups'		=> $lines->groups,
									'is_sold_out'	=> $is_sold_out,
								];
							}

						//保有ポイントのみ存在するとき
						}elseif( !empty($discouns['hold_pt']) && empty($discouns['groups']) ){
							if( $user_param['point'] >= $discouns['hold_pt'] ){
								$exist_flg = true;
								$list_product[] = [
									'delay'			=> $delay,
									'id'			=> $lines->id,
									'tipster'		=> $lines->tipster,
									'tipster_id'	=> $lines->tipster_id,
									'comment'		=> $lines->comment,
									'contents'		=> $lines->contents,
									'title'			=> $lines->title,
									'sub_title'		=> $discouns['title'],
									'saddle'		=> $lines->saddle,
									'tickets'		=> $lines->tickets,
									'quantity'		=> $lines->quantity,
									'point'			=> $lines->point,
									'money'			=> $lines->money,
									'discount'		=> $discouns['money'],
									'groups'		=> $lines->groups,
									'is_sold_out'	=> $is_sold_out,
								];
							}

						//所属グループのみ存在するとき
						}elseif( empty($discouns['hold_pt']) && !empty($discouns['groups']) ){
							if( in_array($user_param['group_id'], $listGroups) ){
								$exist_flg = true;
								$list_product[] = [
									'delay'			=> $delay,
									'id'			=> $lines->id,
									'tipster'		=> $lines->tipster,
									'tipster_id'	=> $lines->tipster_id,
									'comment'		=> $lines->comment,
									'contents'		=> $lines->contents,
									'title'			=> $lines->title,
									'sub_title'		=> $discouns['title'],
									'saddle'		=> $lines->saddle,
									'tickets'		=> $lines->tickets,
									'quantity'		=> $lines->quantity,
									'point'			=> $lines->point,
									'money'			=> $lines->money,
									'discount'		=> $discouns['money'],
									'groups'		=> $lines->groups,
									'is_sold_out'	=> $is_sold_out,
								];
							}
						}else{
							$exist_flg = true;
							$list_product[] = [
								'delay'			=> $delay,
								'id'			=> $lines->id,
								'tipster'		=> $lines->tipster,
								'tipster_id'	=> $lines->tipster_id,
								'comment'		=> $lines->comment,
								'contents'		=> $lines->contents,
								'title'			=> $lines->title,
								'sub_title'		=> '',
								'saddle'		=> $lines->saddle,
								'tickets'		=> $lines->tickets,
								'quantity'		=> $lines->quantity,
								'point'			=> $lines->point,
								'money'			=> $lines->money,
								'discount'		=> $discouns['money'],
								'groups'		=> $lines->groups,
								'is_sold_out'	=> $is_sold_out,
							];
						}
					}
				}else{
					$exist_flg = true;
					$list_product[] = [
						'delay'			=> $delay,
						'id'			=> $lines->id,
						'tipster'		=> $lines->tipster,
						'tipster_id'	=> $lines->tipster_id,
						'comment'		=> $lines->comment,
						'contents'		=> $lines->contents,
						'title'			=> $lines->title,
						'sub_title'		=> '',
						'saddle'		=> $lines->saddle,
						'tickets'		=> $lines->tickets,
						'quantity'		=> $lines->quantity,
						'point'			=> $lines->point,
						'money'			=> $lines->money,
						'discount'		=> $lines->money,
						'groups'		=> $lines->groups,
						'is_sold_out'	=> $is_sold_out,
					];
				}

				if( !$exist_flg ){
					$list_product[] = [
						'delay'			=> $delay,
						'id'			=> $lines->id,
						'tipster'		=> $lines->tipster,
						'tipster_id'	=> $lines->tipster_id,
						'comment'		=> $lines->comment,
						'contents'		=> $lines->contents,
						'title'			=> $lines->title,
						'sub_title'		=> '',
						'saddle'		=> $lines->saddle,
						'tickets'		=> $lines->tickets,
						'quantity'		=> $lines->quantity,
						'point'			=> $lines->point,
						'money'			=> $lines->money,
						'discount'		=> $lines->money,
						'groups'		=> $lines->groups,
						'is_sold_out'	=> $is_sold_out,
					];
				}
			}
		}
		return $list_product;
	}

	/*
	 * 過去の実績、喜びの声から置換文字を変換
	 */
	public function getByTypeReplaceData($lpid = null, $contents, $db_name = null)
	{
		if( is_null($db_name) ){
			$db_data = Achievement::where("race_date", "<", date('Y-m-d'))->orderBy("race_date", 'desc')->where("open_flg", 1)->get();
			$db_voice_data = Voice::where("post_date", "<=", date('Y-m-d'))->orderBy("post_date", 'desc')->where("open_flg", 1)->get();


		//DB名指定
		}else{
			$db_obj = new Achievement;
			$db_obj->setConnection($db_name);
			$db_data = $db_obj->where("race_date", "<", date('Y-m-d'))->orderBy("race_date", 'desc')->where("open_flg", 1)->get();

			$db_obj = new Voice;
			$db_obj->setConnection($db_name);
			$db_voice_data = $db_obj->where("race_date", "<=", date('Y-m-d'))->orderBy("race_date", 'desc')->where("open_flg", 1)->get();
		}

		//過去の実績
		if( count($db_data) > 0 ){
			foreach($db_data as $lines){
				list($year, $mon, $day) = explode("-", $lines->race_date);
				$contents = preg_replace("/\-%%race_name[1-6]\-/", $lines->race_name, $contents, 1);
				$contents = preg_replace("/\-%%race_track[1-6]\-/", $lines->msg3, $contents, 1);
				$contents = preg_replace("/\-%%ticket[1-6]_[1-3]\-/", $lines->msg2, $contents, 1);
				$contents = preg_replace("/\-%%dividend[1-6]_[1-3]\-/", $lines->msg1, $contents, 1);
				$contents = preg_replace("/\-%%holding_date[1-6]\-/", Carbon::create($year, $mon, $day)->format('Y年n月j日'), $contents, 1);
			}
		}

		//ご利用者の声
		if( count($db_voice_data) > 0 ){
			foreach($db_voice_data as $lines){
				list($year, $mon, $day) = explode("-", $lines->post_date);
				$contents = preg_replace("/\-%%voice_name[1-6]\-/", $lines->name, $contents, 1);
				$contents = preg_replace("/\-%%voice_title[1-6]\-/", $lines->title, $contents, 1);
				$contents = preg_replace("/\-%%voice_comment[1-6]\-/", $lines->msg, $contents, 1);
				$contents = preg_replace("/\-%%voice_posting_date[1-6]\-/", Carbon::create($year, $mon, $day)->format('Y年n月j日'), $contents, 1);
				$contents = preg_replace("/\-%%voice_image[1-6]\-/", '/images/voice/'.$lines->img, $contents, 1);
			}
		}

		return $contents;
	}

	/*
	 * ログインボーナスPTを取得
	 */
	public function getLoginBonusInfo()
	{
		$db_data = Grant_point::where('type', 'loginbonus')->where('disp_flg', 1)->first();

		if( !empty($db_data) ){
			return [$db_data->point, $db_data->dispmsg, $db_data->disptime];
		}else{
			return [0, '', 0];			
		}
	}

	/*
	 * お知らせを取得
	 */
	public function getUserInfo()
	{
		$userinfo = [];
		$db_userinfo = User_info::where('disp_flg', 1)->orderBy('order')->get();
		if( count($db_userinfo) > 0 ){
			foreach($db_userinfo as $lines){
				$userinfo[] = $lines;
			}
		}
		return $userinfo;
	}

	/*
	 * アクセス元ドメインからサイト別のconst.phpの文字列を取得する
	 * 引数：サイトごとに存在するconst.phpの中のconst文字列
	 * ※現在、使用されていない
	 */
	public function getSiteConstStr($const_key){
		//アクセス元ドメインがある場合
		if( isset($_SERVER['SERVER_NAME']) ){
			//どのサイトのconst.phpなのかディレクトリ名を取得
			$site_config = config('const.list_site_const')[$_SERVER['SERVER_NAME']];

			//ユーザーサイト側のconst.phpの文字列を返す
			if( isset($site_config) ){
				//const.phpの文字列を返す
				return config($site_config.'.'.$const_key);

			//統合システムのconst.phpの文字列を返す
			}else{
				return config($const_key);
			}
		}
	}

	/*
	 * convert_tablesテーブルから変換文字列リストを取得しキーと値の連想配列を生成
	 * 引数：
	 */
	public function getListConvertKeyValue($db_name = null){
		//%変換文字列を取得
		//デフォルトは管理側のDB
		if( is_null($db_name) ){
			$db_convert_str = Convert_table::where('type', 0)->get();

		//DB名指定
		}else{
			$db_obj = new Convert_table;
			$db_obj->setConnection($db_name);
			$db_convert_str = $db_obj->where('type', 0)->get();
		}

		$listData = [];
		if( count($db_convert_str) > 0 ){
			foreach($db_convert_str as $lines){	
				$listData[$lines->key] = $lines->value;
			}
		}
		
		return $listData;
	}

	/*
	 * convert_tablesテーブルから変換文字列リストを取得し変換を行う
	 * 引数：
	 */
	public function getConvertData($convert_str, $db_name = null){
		//%変換文字列を取得
		//デフォルトは管理側のDB
		if( is_null($db_name) ){
			$db_convert_str = Convert_table::where('type', 0)->get();

		//DB名指定
		}else{
			$db_obj = new Convert_table;
			$db_obj->setConnection($db_name);
			$db_convert_str = $db_obj->where('type', 0)->get();
		}
		
		//%変換処理
		if( count($db_convert_str) > 0 ){
			foreach($db_convert_str as $lines){	
				//%変換処理
				$convert_str = preg_replace("/".preg_quote($lines->key, '/')."/", $lines->value, $convert_str);
			}
		}
		
		return $convert_str;
	}

	/*
	 * 文字列中のURLをリンクに変換
	 * 引数：
	 */
	public function getConvertLink($convert_str){
		
		//%変換処理
		$convert_str = preg_replace("/(https?:\/\/.+)/", "<a href='$1'>$1</a>", $convert_str);
		
		return $convert_str;
	}

	/*
	 * convert_tablesテーブルから変換文字列リストを取得し変換を行う
	 * 引数：メール本文、メール件名
	 */
	public function getMailConvertData($body, $subject, $from_name, $from_mail, $db_name = null){
		//%変換文字列を取得
		//デフォルトは管理側のDB
		if( is_null($db_name) ){
			$db_convert_str = Convert_table::where('type', 0)->get();

		//DB名指定
		}else{
			$db_obj = new Convert_table;
			$db_obj->setConnection($db_name);
			$db_convert_str = $db_obj->where('type', 0)->get();
		}
		
		//%変換処理
		if( count($db_convert_str) > 0 ){
			foreach($db_convert_str as $lines){	
				//%変換処理
				$from_name = preg_replace("/".preg_quote($lines->key, '/')."/", $lines->value, $from_name);
				$from_mail = preg_replace("/".preg_quote($lines->key, '/')."/", $lines->value, $from_mail);
				$subject = preg_replace("/".preg_quote($lines->key, '/')."/", $lines->value, $subject);
				$body	 = preg_replace("/".preg_quote($lines->key, '/')."/", $lines->value, $body);
			}
		}
		
		return [$body, $subject, $from_name, $from_mail];
	}

	/*
	 * 管理ページのデフォルトのパラメータを取得
	 */
	public function getAdminDefaultDispParam()
	{
		//認証情報取得
		$user = \Auth::guard('admin')->user();

		//オブジェクトがなかったらfalse
		if( empty($user) ){
			return false;
		}

		$admin_obj = new Admin;

		//デフォルトのsiteoデータベース接続
		$admin_obj->setConnection('mysql');

		$db_data = $admin_obj->leftJoin('operation_dbs', 'operation_dbs.db', 'admins.select_db')->where('email', $user->email)->first();

		//画面文言用変数
		return [
			'id'			=> $user->id,
			'login_id'		=> $user->email,
			'auth_type'		=> $db_data->type,
			'select_db'		=> $db_data->select_db,
			'select_site'	=> $db_data->name,
		];
	}

	/*
	 * 代理店管理ページのデフォルトのパラメータを取得
	 */
	public function getAgencyDefaultDispParam()
	{
		//認証情報取得
		$user = \Auth::guard('agency')->user();

		//オブジェクトがなかったらfalse
		if( empty($user) ){
			return false;
		}

		$db_data = Agency::where('login_id', $user->login_id)->first();
		
		//画面文言用変数
		return [
			'agency_id'		=> $db_data->id,
			'name'			=> $db_data->name,
			'login_id'		=> $db_data->login_id,
			'password_raw'	=> $db_data->password_raw,
			'memo'			=> $db_data->memo,
		];
	}
	
	/*
	 * 顧客会員ページのデフォルトのパラメータを取得
	 */
	public function getDefaultDispParam()
	{
		//認証情報取得
		$user = \Auth::guard('user')->user();
		
		//画面文言用変数
		return [
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
		];
	}

	/*
	 * メールアドレスが携帯かPCか判定
	 * return 携帯：null PC：false
	 */
	public function judgeMobileDevice($email){
		$MOBILR_MATCH = implode("|", config('const.list_mobile_domain'));

		//PCドメインなら
		if( preg_match("/{$MOBILR_MATCH}/", $email) == 0 ){
			return false;
		}

		//携帯ドメインなら
		return null;
	}

	/*
	 * 的中データ取得
	 * 戻り値：的中DBデータ, 左側を表示する配列データ, 右側を表示する配列データ
	 * return Array(Array, Array, Array)
	 */
	public function getHitAchievements(Request $request, $mode = null)
	{

		//画面に表示する件数
		$disp_limit = config('const.disp_achievements_limit');

		//的中実績データ取得
		$query = Achievement::query();
		$db_data = $query->join('top_products', 'top_products.id', '=', 'achievements.product_id')
				->join('tipsters', 'tipsters.id', '=', 'top_products.tipster')
				->select('achievements.*', 'top_products.*', 'tipsters.name as tipster_name')
				->where('achievements.open_flg', 1)
				->where('achievements.type', 0)
				->orderBy('achievements.race_date', 'desc')
				->get();

		//TOPコンテンツデータ取得
		$query = Achievement::query();
		$db_achievements_data = $query->join('top_contents', 'top_contents.id', '=', 'achievements.product_id')
				->select('achievements.*', 'top_contents.img', 'top_contents.title')
				->where('achievements.open_flg', 1)
				->where('top_contents.open_flg', 1)
				->where('achievements.type', 1)
				->orderBy('achievements.race_date', 'desc')
				->get();

		$listData = [];
		if( count($db_achievements_data) > 0 ){
			setlocale(LC_ALL, 'ja_JP.UTF-8');
			foreach($db_achievements_data as $lines){
				list($year, $mon, $day) = explode("-", !empty($lines->race_date) ? $lines->race_date:date('Y-m-d'));
				$listData[] = [
					'img'		 => !empty($lines->img) ? $lines->img:'',
					'product_id' => !empty($lines->product_id) ? $lines->product_id:'',
					'tipster_id' => '',
					'tipster'	 => !empty($lines->title) ? $lines->title:'',
					'date'		 => Carbon::create($year, $mon, $day)->formatLocalized('%Y/%m/%d (%a)'),
					'sort'		 => $year.$mon.$day,
					'name'		 => !empty($lines->race_name) ? $lines->race_name:'',
					'msg1'		 => !empty($lines->msg1) ? $lines->msg1:'',
					'msg2'		 => !empty($lines->msg2) ? $lines->msg2:'',
					'msg3'		 => !empty($lines->msg3) ? $lines->msg3:'',
					'memo'		 => !empty($lines->memo) ? $lines->memo:'',
				];
			}
		}

		if( !empty($db_data) ){
			setlocale(LC_ALL, 'ja_JP.UTF-8');
			foreach($db_data as $lines){
				list($year, $mon, $day) = explode("-", !empty($lines->race_date) ? $lines->race_date:date('Y-m-d'));
				$listData[] = [
					'product_id' => !empty($lines->product_id) ? $lines->product_id:'',
					'tipster_id' => !empty($lines->tipster) ? $lines->tipster:'',
					'tipster'	 => !empty($lines->tipster_name) ? $lines->tipster_name:'',
					'date'		 => Carbon::create($year, $mon, $day)->formatLocalized('%Y/%m/%d (%a)'),
					'sort'		 => $year.$mon.$day,
					'name'		 => !empty($lines->race_name) ? $lines->race_name:'',
					'msg1'		 => !empty($lines->msg1) ? $lines->msg1:'',
					'msg2'		 => !empty($lines->msg2) ? $lines->msg2:'',
					'msg3'		 => !empty($lines->msg3) ? $lines->msg3:'',
					'memo'		 => !empty($lines->memo) ? $lines->memo:'',
				];
			}
		}

		//日付でソート
		foreach ($listData as $key => $lines) {
			$listSort[] = $lines['sort'];
		}
		array_multisort($listSort, SORT_REGULAR, SORT_DESC, $listData, SORT_REGULAR, SORT_DESC);

		$pager = null;

		//ページャー生成
		if( is_null($mode) ){
			if( empty($request->page) ){
				$request->page = 1;
			}
			$limit = config('const.disp_achievements_limit');
			$pager = $this->getMakePager($request->url(), $listData, $request->page, $limit);
			$listData = array_slice($listData, ($request->page - 1) * $limit, $limit);
		}

		return [$pager, $listData];
	}

	/*
	 *	ページャーを生成
	 *	引数：アクセスURL、表示用配列データ、現在ページ、表示件数
	 *	戻り値：ページャー用のパラメータ
	 *	用途：bladeファイルでページャーを生成したい箇所で@includeでvendor配下のページャー用のbladeファイルを読み込むときに
	 *		この結果パラメータを引数として渡す。
	 *	対応blade：vendor/achievements.blade.php
	 */
	public function getMakePager($access_url, $listData, $current_page, $disp_limit = 10)
	{
		//最初のページ
		if( empty($current_page) ){
			$current_page = 1;
		}

		//最後のページ
		$last_page = ceil(count($listData) / $disp_limit);

		//ページごとのデータ
		for($page=1;$page<=$last_page;$page++){
			$listPage[$page] = $access_url.'?page='.$page;
		}

		//前ページ・次ページ
		$nextPage = $current_page + 1;
		$prevPage = $current_page - 1;

		//前ページが0以下なら
		if( $prevPage <= 0 ){
			$prevPage = 1;
		}

		//次ページが最後以降なら
		if( $nextPage >= $last_page ){
			$nextPage = $last_page;
		}

		return [
			'current_page'	=> $current_page,
			'last_page'		=> $last_page,
			'next_url'		=> $access_url.'?page='.$nextPage,
			'prev_url'		=> $access_url.'?page='.$prevPage,
			'elements'		=> $listPage
		];
	}
	
	/*
	 * 喜びの声データ取得
	 * 戻り値：喜びの声の配列データ, パージャー
	 * return Array, String
	 */
	public function getVoice()
	{
		//画面に表示する件数
		$disp_limit = config('const.disp_achievements_limit');

		//お客様の声データ取得
		$db_data = Voice::where('open_flg', 1)->orderBy('post_date', 'desc')->paginate($disp_limit);

		$listData = [];

		if( !empty($db_data) ){
			setlocale(LC_ALL, 'ja_JP.UTF-8');
			foreach($db_data as $lines){
				//投稿日時
				list($year, $mon, $day) = explode("-", !empty($lines->post_date) ? $lines->post_date:date('y-m-d'));

				//タイトル
				$title = !empty($lines->title) ? $lines->title:'';
				if( empty($lines->title) ){
					$title = config('const.none_post_title');
				}

				//投稿者
				$name = !empty($lines->name) ? $lines->name:'';
				if( empty($lines->name) ){
					$name = config('const.none_post_name');
				}

				//画面表示変数
				$listData[] = [
					'post_date' => Carbon::create($year, $mon, $day)->formatLocalized('%Y年%m月%d日(%a)'),
					'title' => $title,
					'name' => $name,
					'msg' => !empty($lines->msg) ? $lines->msg:'',
					'img' => !empty($lines->img) ? $lines->img:''
				];
			}
		}

		return [$listData, $db_data->links()];
	}

	public function calcFileSize($size)
	{
		$b = 1024;    // バイト
		$mb = pow($b, 2);   // メガバイト
		$gb = pow($b, 3);   // ギガバイト

		switch(true){
			case $size >= $gb:
				$target = $gb;
				$unit = 'GB';
				break;
			case $size >= $mb:
				$target = $mb;
				$unit = 'MB';
				break;
			default:
				$target = $b;
				$unit = 'KB';
			break;
		}

		$new_size = round($size / $target, 2);
		$file_size = number_format($new_size, 2, '.', ',') . $unit;

		return $file_size;
	}

	/*
	 * メールアドレスに禁止ワードがかるかどうかチェック
	 */
	public function checkNgWordEmail($email, $db_name = null)
	{
		//デフォルトは管理側のDB
		if( is_null($db_name) ){
			$db_data = Email_ng_word::where('type', 'mail')->first();

		//DB名指定
		}else{
			$db_obj = new Email_ng_word;
			$db_obj->setConnection($db_name);
			$db_data = $db_obj->where('type', 'mail')->first();
		}

		if( !empty($db_data->word) ) {
			$ng_word_match = preg_replace("/,/", "|", preg_quote($db_data->word, '/'));
			if( preg_match("/{$ng_word_match}/", $email) > 0 ){
				return false;
			}
		}
		return null;
	}

	/*
	 * 設定されているSMTPを取得
	 */
	public function getSmtpHost($type)
	{
		$smtp_ip = null;
		$port = null;

		$db_data = Relay_server::where('type', $type)->first();
		if( !empty($db_data->ip) ){
			$smtp_ip = $db_data->ip;
		}

		if( !empty($db_data->port) ){
			$port = $db_data->port;
		}

		return [$smtp_ip, $port];
	}

	/*
	 * バナーを取得する
	 */
	public function getBanner()
	{
		//バナーデータ取得
		$db_banner_data = Banner::where('disp_flg', 1)
		->orderBy('created_at','asc')->get();

		//データがあれば
		if( !empty($db_banner_data) ){
			//javascriptタグの開始終了タグをエスケープ
			foreach($db_banner_data as $lines){
				$lines->banner = Utility::escapeJsTag($lines->banner);
			}
		}

		return $db_banner_data;
	}

	/*
	 *	javascriptの開始終了タグをエスケープする
	 */
	public function escapeJsTag($contents)
	{
		if( !empty($contents) ){
			return preg_replace("/<(.*?)script(.*?)>/", "&lt;$1script$2&gt;", $contents);
		}
	}

	/*
	 *	MXドメインが存在するか確認
	 */
	public function checkMxDomain($email)
	{
		list($account, $mail_domain) = explode("@", trim($email));

		//MXドメインを取得
		$exist_flg = getmxrr($mail_domain, $listMxDomain);

		//MXドメインがない場合
		if( !$exist_flg ){
			return false;
		}

		return null;
	}

	/*
	 * 現在時刻が引数で指定した期間内かどうかチェック
	 * $start_date:yyyy-mm-dd hh:mm:ss(開始時刻)
	 * $end_date:yyyy-mm-dd hh:mm:ss(終了時刻)
	 */
	function checkNowDateWithinPeriod($start_date, $end_date){
		$start_date = new Carbon($start_date);
		$end_date = new Carbon($end_date);

		$now_date = Carbon::now();

		//期間内：true
		//期間外：false
		return Carbon::parse($now_date)->between($start_date, $end_date);
	}

}

