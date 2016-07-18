<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

session_start();
//error_reporting(E_ALL ^ E_DEPRECATED);

require_once 'php/config.php';

date_default_timezone_set(TIME_ZONE);

require_once "php/ezSQL/shared/ez_sql_core.php";
require_once "php/ezSQL/ez_sql_pdo.php";
require_once 'php/db.php';
require_once 'php/string.php';
require_once 'php/layout.php';
require_once 'php/class.phpmailer.php';
require_once 'php/functions.php';

$db = new Db;
$tmpl_strings = new StringResource('str/');
