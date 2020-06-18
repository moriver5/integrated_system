<?php

namespace RenatoMarinho\LaravelPageSpeed\Middleware;

class TrimUrls extends PageSpeed
{
    public function apply($buffer)
    {
        $replace = [
/*
 * 諸事情で削除されないようにコメント
 * add 2019/5/27 by moritomo
 */
//            '/https:/' => '',
//            '/http:/' => ''
        ];

        return $this->replace($replace, $buffer);
    }
}
