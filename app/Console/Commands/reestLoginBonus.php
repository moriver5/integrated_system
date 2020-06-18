<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class reestLoginBonus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:login_bonus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '毎日０時にusersテーブルのbonus_flgを１→０にする';

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
		$listSiteDb = DB::select("select db from operation_dbs");

		if( count($listSiteDb) > 0 ){
			foreach($listSiteDb as $db_lines){
				$db_name = $db_lines->db;
				DB::connection($db_name)->update('update users set bonus_flg = 0;');
			}
		}

    }
}
