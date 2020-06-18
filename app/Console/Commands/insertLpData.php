<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Model\Landing_page;
use App\Model\Landing_pages_content;
use App\Model\Landing_pages_preview;
use Session;
use Utility;
use DB;
use File;
use Carbon\Carbon;

class insertLpData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:lp {landing_csvfile} {landing_detail_csvfile} {dbname}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'あらかじめ保存しておいたランディングページのCSVデータを移行先データベースとランディングページ格納フォルダへ保存する';

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
        $csvfile1 = $this->argument("landing_csvfile");				//landingテーブルのCSVデータ
        $csvfile2 = $this->argument("landing_detail_csvfile");		//landing_detailテーブルのCSVデータ
		$db_name = $this->argument("dbname");

		$now_datetime = new Carbon();
		$now_date = preg_replace("/(\d{4}\-\d{2}\-\d{2})\s\d{2}:\d{2}:\d{2}/", "$1", $now_datetime);

		$file = new \SplFileObject(storage_path('csv_data/'.$csvfile1));

		$file->setFlags(
			\SplFileObject::READ_CSV |			// CSV 列として行を読み込む
			\SplFileObject::READ_AHEAD |		// 先読み/巻き戻しで読み出す。
			\SplFileObject::SKIP_EMPTY |		// 空行は読み飛ばす
			\SplFileObject::DROP_NEW_LINE		// 行末の改行を読み飛ばす
		 );

		$listData = [];
		$listLpData = [];

		foreach($file as $lpno => $lines){
			if( $lpno == 0 )
				continue;
echo $lpno."\n";

			//ランディングページ用のフォルダを作成
			system("mkdir ".config('const.project_storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_dir_path').'/'.$lpno.';sudo chown -R apache:apache '.config('const.project_storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_dir_path').'/'.$lpno.';sudo chmod -R 775 '.config('const.project_storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_dir_path').'/'.$lpno.';');
			system("mkdir ".config('const.project_storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_dir_path').'/'.$lpno.'/pc;sudo chown -R apache:apache '.config('const.project_storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_dir_path').'/'.$lpno.'/pc;sudo chmod -R 775 '.config('const.project_storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_dir_path').'/'.$lpno.'/pc;');
			system("mkdir ".config('const.project_storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_dir_path').'/'.$lpno.'/sp;sudo chown -R apache:apache '.config('const.project_storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_dir_path').'/'.$lpno.'/sp;sudo chmod -R 775 '.config('const.project_storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_dir_path').'/'.$lpno.'/sp;');
			system("mkdir ".config('const.project_storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_dir_path').'/'.$lpno.'/mb;sudo chown -R apache:apache '.config('const.project_storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_dir_path').'/'.$lpno.'/mb;sudo chmod -R 775 '.config('const.project_storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_dir_path').'/'.$lpno.'/mb;');
			system("mkdir ".config('const.project_storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_dir_path').'/'.$lpno.'/pcsp;sudo chown -R apache:apache '.config('const.project_storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_dir_path').'/'.$lpno.'/pcsp;sudo chmod -R 775 '.config('const.project_storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_dir_path').'/'.$lpno.'/pcsp;');

			//シンボリックリンクを張る
			if( !empty($lines[1]) ){
				system("ln -fns ".config('const.project_storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_url_path').'/'.$lpno.' '.config('const.project_home_path').'/'.config('const.public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_url_path').'/');
			}

			$listData[] = [
				'open_flg'	=> $lines[1],
				'memo'		=> $lines[4],
				'sort_date'	=> $now_date,
				'created_at'=> $now_datetime,
				'updated_at'=> $now_datetime,
			];

			foreach(config('const.lp_default_page') as $name){
				for($i=0;$i<=3;$i++){
					//データ登録
					$lp_content = new Landing_pages_content([
						'lp_id'				=> $lpno,
						'type'				=> $i,
						'name'				=> $name,
						'created_at'		=> $now_datetime,
						'updated_at'		=> $now_datetime
					]);

					//DB接続
					$lp_content->setConnection($db_name);

					//DB保存
					$lp_content->save();
				}
			}
		}

		DB::connection($db_name)->table('landing_pages')->insert($listData);


		$listData = [];

		$file = new \SplFileObject(storage_path('csv_data/'.$csvfile2));

		$file->setFlags(
			\SplFileObject::READ_CSV |			// CSV 列として行を読み込む
			\SplFileObject::READ_AHEAD |		// 先読み/巻き戻しで読み出す。
			\SplFileObject::SKIP_EMPTY |		// 空行は読み飛ばす
			\SplFileObject::DROP_NEW_LINE		// 行末の改行を読み飛ばす
		 );

		$listCareer = [
			0 => 'pc',
			1 => 'mb',
			2 => 'sp',
			3 => 'pcsp',
		];

		foreach($file as $lpno => $lines){
//print_r($lines);
			if( $lpno == 0 )
				continue;
//echo $lpno."\n";
			$count = DB::connection($db_name)->table('landing_pages_contents')->where('lp_id', $lines[1])->count();

			if( $count > 0 ){
				DB::connection($db_name)->table('landing_pages_contents')
					->where('lp_id', $lines[1])
					->where('type', $lines[2])
					->where('name', $lines[3])
					->update([
						'url_open_flg'	=> $lines[5],
						'content'		=> $lines[4]
					]);

				//ファイルに書き込み
				$file_size = File::put(config('const.project_storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_url_path').'/'.$lines[1].'/'.$listCareer[$lines[2]].'/'.$lines[3], $lines[4]);

				//シンボリックリンクを張る
				if( !empty($lines[5]) ){
					system("ln -fns ".config('const.project_storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_url_path').'/'.$lines[1].'/'.$listCareer[$lines[2]].'/'.$lines[3].' '.config('const.project_home_path').'/'.config('const.public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_url_path').'/'.$lines[1].'/'.$listCareer[$lines[2]].'/');
					system("ln -fns ".config('const.project_storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_url_path').'/.htaccess '.config('const.project_storage_home_path').'/'.config('const.storage_public_dir_path').'/'.config('const.list_domain_const')[$db_name].'/'.config('const.landing_url_path').'/'.$lines[1].'/');
				}
			}
		}

//		DB::connection($db_name)->table('landing_pages_contents')->insert($listData);
    }
}
