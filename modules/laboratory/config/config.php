<?php 
    
    define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'));
    define('CURRENT_URI',basename($_SERVER['REQUEST_URI']));
    define('CONFIG_PATH',BASE_URL.'/config');
