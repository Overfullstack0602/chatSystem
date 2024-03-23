<?php


define("ROOT", $_SERVER['DOCUMENT_ROOT']);

require_once ROOT . "/classes/Database.php";

require_once ROOT . "/classes/Session.php";
require_once ROOT . "/classes/Home.php";
require_once ROOT . "/classes/User.php";
require_once ROOT . "/classes/Message.php";

Session::start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/libs/hashids/vendor/autoload.php";
use Hashids\Hashids;
$hashids = new Hashids('', 15);