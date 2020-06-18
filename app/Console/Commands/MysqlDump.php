<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class MysqlDump extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dump:get {db_name} {user_id} {password} {db_host} {full_path_save_file}';

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
		$db_name	 = $this->argument('db_name');
		$user_id	 = $this->argument('user_id');
		$password	 = $this->argument('password');
		$db_host	 = $this->argument('db_host');
		$save_file	 = $this->argument('full_path_save_file');

		$process = system("mysqldump -u{$user_id} -p{$password} -h {$db_host} -t {$db_name} > {$save_file}");
    }
}
