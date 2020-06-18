<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Routing\Middleware;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\View\FileViewFinder;

class ViewSwitchMiddleware
{
	/**
	 * The view factory implementation.
	 *
	 * @var \Illuminate\Contracts\View\Factory
	 */
	protected $view;

	/**
	 * Create a new instance.
	 *
	 * @param  \Illuminate\Contracts\View\Factory  $view
	 * @return void
	 */
	public function __construct(ViewFactory $view)
	{
		$this->view = $view;
	}
	
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		//アクセス元ドメインからユーザー側のviewディレクトリを振り分ける
		if( isset($_SERVER['SERVER_NAME']) ){
			$app = app();

			//設定値がなければエラー画面表示
			if( empty($app['config']['const']['list_site_const'][$_SERVER['SERVER_NAME']]) ){
				abort('403');
			}

			/*
			 *	resources/lang配下のディレクトリをアクセス元ホストで振り分ける
			 */
			$app->setLocale($app['config']['const']['list_site_const'][$_SERVER['SERVER_NAME']]);

			/*
			 *	権限ごとのディレクトリを先に検索させるため、パス情報の先頭へ
			 */
			$paths = $app['config']['view.paths'];
//			array_unshift($paths, $app['config']['view.paths'][0].'/'.$_SERVER['SERVER_NAME']);
			array_unshift($paths, $app['config']['view.paths'][0].'/'.$app['config']['const']['list_site_const'][$_SERVER['SERVER_NAME']]);

			/*
				パスを変更するためFileViewFinderを生成
				ただし、そのまま新しいFileViewFinderをセットするとすでにセットされたhint情報が消えてしまうので
				すでに設定済みのFileViewFinderのhintをコピーしてからセットする
				(ページャーや通知でhint情報をいれている)
			 */
			$finder = new FileViewFinder($app['files'], $paths);
			$old_finder = $this->view->getFinder();
			foreach ($old_finder->getHints() as $namespace => $hint) {
				$finder->addNamespace($namespace, $hint);
			}

			$this->view->setFinder($finder);
		}

		return $next($request);
	}
}
