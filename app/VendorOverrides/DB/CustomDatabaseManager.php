<?php

namespace App\VendorOverrides\DB;

use Illuminate\Database\DatabaseManager;
use Session;

class CustomDatabaseManager extends DatabaseManager
{
    /**
     * Get a database connection instance.
     *
     * @param  string  $name
     * @return \Illuminate\Database\Connection
     */
    public function connection($name = null)
    {
		//ソース追加
		//ユーザーからのアクセスをアクセス元ドメインで自動で切換える
		//<-- start -->
		if( empty(Session::get('operation_select_db')) ){
			//database.phpからすべてのデータベース設定を配列で読み込む
//			$db_config = \Config::get('database.connections');
			$db_config = \Config::get('const')['list_site_const'];
//error_log(print_r($db_config,true).":db\n",3,"/data/www/storage/siteo/storage/logs/nishi_log.txt");

			//アクセス元ドメインで管理者かユーザーかを判定
			//ユーザーがアクセスしたサイトのデータベース
			if( !empty($_SERVER['SERVER_NAME']) && !empty($db_config[$_SERVER['SERVER_NAME']]) ){			
				$name = $db_config[$_SERVER['SERVER_NAME']];

			//統合システムのデータベースを設定
			}else{
				//$nameの指定がなければデフォルトのmysqlを使用
				if( is_null($name) ){
					$name = 'mysql';
				}
			}
		}
		//<-- end -->

        [$database, $type] = $this->parseConnectionName($name);

        $name = $name ?: $database;
//error_log("{$name}, {$database}, {$type} :db\n",3,"/data/www/storage/siteo/storage/logs/nishi_log.txt");
        // If we haven't created this connection, we'll create it based on the config
        // provided in the application. Once we've created the connections we will
        // set the "fetch mode" for PDO which determines the query return types.
        if (! isset($this->connections[$name])) {
            $this->connections[$name] = $this->configure(
                $this->makeConnection($database), $type
            );
        }

        return $this->connections[$name];
    }





}
