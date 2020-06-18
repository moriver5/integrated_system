<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Symfony\Component\HttpFoundation\Cookie;

class VerifyCsrfToken extends BaseVerifier
{
	/**
	 * The URIs that should be excluded from CSRF verification.
	 *
	 * @var array
	 */
	protected $except = [
		//CSRF保護を除外するURI
		'/admin/logout',
		'/LP/*',
//		'/regist',
//		'/admin/member/client/edit/send',	//テストに使用
//		'/member/expectation/toll/view',	//テストに使用
	];

    protected function addCookieToResponse($request, $response)
    {
        $config = config('session');

        $response->headers->setCookie(
            new Cookie(
                'XSRF-TOKEN', $request->session()->token(), $this->availableAt(60 * $config['lifetime']),
                $config['path'], $config['domain'], $config['secure'], true, true, $config['same_site'] ?? null
            )
        );

        return $response;
    }

}
