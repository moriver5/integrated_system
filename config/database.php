<?php
	//テスト環境
	if( $_SERVER['HOSTNAME'] == 'dev-php01' ){
		$site_db_connect = [
			'connections' => [
				'mysql' => [
					'driver' => 'mysql',
					'host' => env('DB_HOST', '172.16.0.36'),
					'port' => env('DB_PORT', '3306'),
					'database' => env('DB_DATABASE', 'siteo'),
					'username' => env('DB_USERNAME', 'elephpant'),
					'password' => env('DB_PASSWORD', 'R6qCATWH'),
					'unix_socket' => env('DB_SOCKET', ''),
					'charset' => 'utf8',
					'collation' => 'utf8_general_ci',
					'prefix' => '',
					'strict' => false,
					'engine' => null,
		//PDOの持続的な接続を使用したい場合はtrue
		//			'options'   => [
		//				PDO::ATTR_PERSISTENT => true,
		//			],
				],
				'gsta' => [
					'driver' => 'mysql',
					'host' => env('DB_HOST', '172.16.0.36'),
					'port' => env('DB_PORT', '3306'),
					'database' => 'gsta',
					'username' => 'elephpant',
					'password' => 'elephpant5963',
					'unix_socket' => '',
					'charset' => 'utf8mb4',
					'collation' => 'utf8mb4_bin',
					'prefix' => '',
					'strict' => false,
					'engine' => null,
		//PDOの持続的な接続を使用したい場合はtrue
		//			'options'   => [
		//				PDO::ATTR_PERSISTENT => true,
		//			],
				],

				'jray' => [
					'driver' => 'mysql',
					'host' => env('DB_HOST', '192.168.0.100'),
					'port' => env('DB_PORT', '3306'),
					'database' => 'keiba_db',
					'username' => 'php_db',
					'password' => 'R6qCATWH',
					'unix_socket' => '',
					'charset' => 'utf8',
					'collation' => 'utf8_general_ci',
					'prefix' => '',
					'strict' => false,
					'engine' => null,
		//PDOの持続的な接続を使用したい場合はtrue
		//			'options'   => [
		//				PDO::ATTR_PERSISTENT => true,
		//			],
				],

				'mysql_premium' => [
					'driver' => 'mysql',
					'host' => env('DB_PREMIUM_HOST', '192.168.0.100'),
					'port' => env('DB_PREMIUM_PORT', '3306'),
					'database' => env('DB_PREMIUM_DATABASE', 'dos'),
					'username' => env('DB_PREMIUM_USERNAME', 'php_db'),
					'password' => env('DB_PREMIUM_PASSWORD', 'R6qCATWH'),
					'unix_socket' => env('DB_SOCKET', ''),
					'charset' => 'utf8',
					'collation' => 'utf8_general_ci',
					'prefix' => '',
					'strict' => false,
					'engine' => null,
				],
			]
		];
	}else{
		$site_db_connect = [
			'connections' => [
				'mysql' => [
					'driver' => 'mysql',
					'host' => env('DB_HOST', '192.168.0.100'),
					'port' => env('DB_PORT', '3306'),
					'database' => env('DB_DATABASE', 'siteo'),
					'username' => env('DB_USERNAME', 'php_db'),
					'password' => env('DB_PASSWORD', 'R6qCATWH'),
					'unix_socket' => env('DB_SOCKET', ''),
					'charset' => 'utf8',
					'collation' => 'utf8_general_ci',
					'prefix' => '',
					'strict' => false,
					'engine' => null,
		//PDOの持続的な接続を使用したい場合はtrue
		//			'options'   => [
		//				PDO::ATTR_PERSISTENT => true,
		//			],
				],
				'gsta' => [
					'driver' => 'mysql',
					'host' => env('DB_HOST', '192.168.0.100'),
					'port' => env('DB_PORT', '3306'),
					'database' => 'gsta',
					'username' => 'php_db',
					'password' => 'R6qCATWH',
					'unix_socket' => '',
					'charset' => 'utf8mb4',
					'collation' => 'utf8mb4_bin',
					'prefix' => '',
					'strict' => false,
					'engine' => null,
		//PDOの持続的な接続を使用したい場合はtrue
		//			'options'   => [
		//				PDO::ATTR_PERSISTENT => true,
		//			],
				],

				'jray' => [
					'driver' => 'mysql',
					'host' => env('DB_HOST', '192.168.0.100'),
					'port' => env('DB_PORT', '3306'),
					'database' => 'php_db',
					'username' => 'elephpant',
					'password' => 'R6qCATWH',
					'unix_socket' => '',
					'charset' => 'utf8',
					'collation' => 'utf8_general_ci',
					'prefix' => '',
					'strict' => false,
					'engine' => null,
		//PDOの持続的な接続を使用したい場合はtrue
		//			'options'   => [
		//				PDO::ATTR_PERSISTENT => true,
		//			],
				],

				'mysql_premium' => [
					'driver' => 'mysql',
					'host' => env('DB_PREMIUM_HOST', '192.168.10.93'),
					'port' => env('DB_PREMIUM_PORT', '3306'),
					'database' => env('DB_PREMIUM_DATABASE', 'dos'),
					'username' => env('DB_PREMIUM_USERNAME', 'php_db'),
					'password' => env('DB_PREMIUM_PASSWORD', 'R6qCATWH'),
					'unix_socket' => env('DB_SOCKET', ''),
					'charset' => 'utf8',
					'collation' => 'utf8_general_ci',
					'prefix' => '',
					'strict' => false,
					'engine' => null,
				],
			]
		];
	}

return array_merge($site_db_connect, [

	/*
	|--------------------------------------------------------------------------
	| Default Database Connection Name
	|--------------------------------------------------------------------------
	|
	| Here you may specify which of the database connections below you wish
	| to use as your default connection for all database work. Of course
	| you may use many connections at once using the Database library.
	|
	*/

	'default' => env('DB_CONNECTION', 'mysql'),

	/*
	|--------------------------------------------------------------------------
	| Database Connections
	|--------------------------------------------------------------------------
	|
	| Here are each of the database connections setup for your application.
	| Of course, examples of configuring each database platform that is
	| supported by Laravel is shown below to make development simple.
	|
	|
	| All database work in Laravel is done through the PHP PDO facilities
	| so make sure you have the driver for your particular database of
	| choice installed on your machine before you begin development.
	|
	*/

	/*
	|--------------------------------------------------------------------------
	| Migration Repository Table
	|--------------------------------------------------------------------------
	|
	| This table keeps track of all the migrations that have already run for
	| your application. Using this information, we can determine which of
	| the migrations on disk haven't actually been run in the database.
	|
	*/

	'migrations' => 'migrations',

	/*
	|--------------------------------------------------------------------------
	| Redis Databases
	|--------------------------------------------------------------------------
	|
	| Redis is an open source, fast, and advanced key-value store that also
	| provides a richer set of commands than a typical key-value systems
	| such as APC or Memcached. Laravel makes it easy to dig right in.
	|
	*/

	'redis' => [

		'client' => 'predis',

		'default' => [
			'host' => env('REDIS_HOST', '127.0.0.1'),
			'password' => env('REDIS_PASSWORD', null),
			'port' => env('REDIS_PORT', 6379),
			'database' => 0,
		],

	],

]);
