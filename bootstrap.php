<?php

date_default_timezone_set('Europe/Moscow');

error_reporting(E_ALL);

ini_set("display_error", true);
ini_set("error_reporting", E_ALL);

require_once 'vendor/autoload.php';
require_once 'functions/template.php';
require_once 'functions/validators.php';
require_once 'functions/db.php';
require_once 'functions/dbget.php';
require_once 'functions/dbadd.php';
require_once 'functions/dbfind.php';
require_once 'functions/requests.php';
require_once 'functions/file.php';
require_once 'functions/mail.php';
require_once 'functions/append.php';
require_once 'functions/click.php';

if (!file_exists('config.php')) {
    exit('Создайте файл config.php на основе config.sample.php и сконфигурируйте его');
}
$config = require 'config.php';

$connect =  dbConnect($config);

session_start();
