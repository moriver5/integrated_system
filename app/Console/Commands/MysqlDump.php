<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Carbon\Carbon;
use File;

class MysqlDump extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dump:get {db_name?} {user_id?} {password?} {db_host?} {save_file?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'データベースのダンプ';

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
		$dt = new Carbon();
		$now_date = $dt->format('Ymd');
		$del_date = $dt->subDays(7)->format('Ymd');

		$backup_path = "/data/www/storage/siteo/db_backup/";

		$db_name	 = $this->argument('db_name');
		$user_id	 = $this->argument('user_id');
		$password	 = $this->argument('password');
		$db_host	 = $this->argument('db_host');
		$save_file	 = $this->argument('save_file');

		if( empty($db_name) ){
			$db_name = 'gsta';
		}
		if( empty($user_id) ){
			$user_id = 'php_db';
		}
		if( empty($password) ){
			$password = 'R6qCATWH';
		}
		if( empty($db_host) ){
			$db_host = '192.168.0.100';
		}
		if( empty($save_file) ){
			$save_file = 'gsta_dump';
		}

		$process = system("mysqldump -u{$user_id} -p{$password} -h {$db_host} -t {$db_name} --databases --triggers --routines --events > {$backup_path}{$save_file}{$now_date}");
//		$process = system("mysqldump -u{$user_id} -p{$password} -h {$db_host} -t {$db_name} --set-gtid-purged=OFF --databases --triggers --routines --events > {$backup_path}{$save_file}{$now_date}");

		if ( \File::exists($backup_path.$save_file.$del_date) ) {
			$process = system("rm {$backup_path}{$save_file}{$del_date}");
		}
    }
}
