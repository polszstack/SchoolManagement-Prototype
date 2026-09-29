<?php

use App\Core\Env;
use App\Core\Database;


// Folder Path (Constants)

$isCli = php_sapi_name() === 'cli';

define('CURRENT_URI',$isCli ? '' : basename($_SERVER['REQUEST_URI']));
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH',BASE_PATH.'/public');
define('APP_PATH', BASE_PATH . '/app');
define('CORE_PATH', APP_PATH . '/core');
define('CONFIG_PATH', APP_PATH . '/config');
define('VIEW_PATH', APP_PATH . '/views');
define('CACHE_PATH',BASE_PATH.'/cache');
define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));

// autoloading 
require_once BASE_PATH . '/vendor/autoload.php'; 

// Environment Config
Env::load(BASE_PATH .'/.env');

// if cache file not exist , make one 

if (!is_dir(CACHE_PATH . '/twig')) {
    mkdir(CACHE_PATH . '/twig', 0775, true);
}
// database 
$config = include CONFIG_PATH . '/database.php';
Database::init($config);













