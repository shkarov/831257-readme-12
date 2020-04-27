<?php

date_default_timezone_set('Europe/Moscow');

require_once 'functions/template.php';
require_once 'functions/validators.php';
require_once 'functions/db.php';

error_reporting(E_ALL);

ini_set("display_error", true);
ini_set("error_reporting", E_ALL);

const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASSWORD = 'klop0987';
const DB_NAME = 'readme';
