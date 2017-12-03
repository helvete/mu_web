<?php

require("restler".DIRECTORY_SEPARATOR."restler.php");
require("..".DIRECTORY_SEPARATOR."config.php");
require("Factory.php");
require("endpoints".DIRECTORY_SEPARATOR."api.php");

//endpoints -> todo autoloader
require("endpoints".DIRECTORY_SEPARATOR."Player.php");

if(!isset($_REQUEST['key']) || $_REQUEST['key'] != $api_auth_key){
  die();
} else {
  unset($_REQUEST['key']);
}

$r = new Restler();
$r->addAPIClass('Player', 'player/');
$r->handle();

//require("api.php");
//$api = new Api($msconnect);
//$api->run();

