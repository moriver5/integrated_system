<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;						//Laraverl5.4のJSONエラーフォーマットをそのまま使用したい場合は追加
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		\Illuminate\Auth\AuthenticationException::class,
		\Illuminate\Auth\Access\AuthorizationException::class,
		\Symfony\Component\HttpKernel\Exception\HttpException::class,
		\Illuminate\Database\Eloquent\ModelNotFoundException::class,
		\Illuminate\Session\TokenMismatchException::class,
		\Illuminate\Validation\ValidationException::class,
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param  \Exception  $exception
	 * @return void
	 */
	public function report(Exception $exception)
	{
		parent::report($exception);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Exception  $exception
	 * @return \Illuminate\Http\Response
	 */
	public function render($request, Exception $exception)
	{
		return parent::render($request, $exception);
	}

	/**
	 * Convert an authentication exception into an unauthenticated response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Illuminate\Auth\AuthenticationException  $exception
	 * @return \Illuminate\Http\Response
	 */
	protected function unauthenticated($request, AuthenticationException $exception)
	{
		if ($request->expectsJson()) {
			return response()->json(['error' => 'Unauthenticated.'], 401);
		}

		switch($exception->guards()[0]){
			//管理画面
			case 'admin':
				return redirect('admin/login');

			case 'agency':
				return redirect('agency/login');

			//ユーザー画面
			case 'user':
//				return redirect()->guest(route('/'));
				return redirect('/');
			default:
		}
	}

	/**
	 * Laraverl5.4のJSONエラーフォーマットをそのまま使用したい場合は追加
	 * バリデーション例外をJSONレスポンスへ変換
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Illuminate\Validation\ValidationException  $exception
	 * @return \Illuminate\Http\JsonResponse
	 */
	protected function invalidJson($request, ValidationException $exception)
	{
		return response()->json($exception->errors(), $exception->status);
	}

}
