<?php

namespace App\Console\Commands\Mail;

use App\Model\User;
use DB;
use Carbon\Carbon;


$strMail = "";
$stdin = fopen("php://stdin", "r");
if (!$stdin){
    //標準入力オープンエラー
    exit();
}
while (!feof ($stdin)){
    $strMail .= fgets($stdin, 4096);
}
fclose ($stdin);

