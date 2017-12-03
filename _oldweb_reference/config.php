<?php

//$dbhost = "127.0.0.1";

$dbuser = "";

$dbpasswd = "";

$host = "MuOnline";

//$port = "55901";

//$regsubmit = "regform.submit()";
$msconnect=odbc_connect("$host","$dbuser","$dbpasswd");

$renabeasthunger = 15;
$bolbeasthunger = 15;
$jocbeasthunger = 30;

$excellbotzen = 666;
$luckbotzen = 222;
$bizubotzen = 777;

$root_dir = dirname(dirname(__FILE__));
$game_dir = $root_dir . "MuServer";
$commonserver_path = $game_dir . "\\data\\commonserver.cfg";

$api_auth_key = "AQUAgoldCROSSbowRULESwithRUM";
//$ip = $host;
?>
