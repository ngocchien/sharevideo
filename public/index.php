<?php
error_reporting(E_COMPILE_ERROR | E_RECOVERABLE_ERROR | E_ERROR | E_CORE_ERROR);
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_language('uni');
mb_regex_encoding('UTF-8');
ob_start('mb_output_handler');
chdir(dirname(__DIR__));

defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));

$staticURL = APPLICATION_ENV === 'production' ? 'http://static.sharevideoclip.com' : 'http://dev.st.sharevideoclip.com';
$baseURL = APPLICATION_ENV === 'production' ? 'http://sharevideoclip.com' : 'http://dev.sharevideoclip.com';
$uploadURL = APPLICATION_ENV === 'production' ? 'http://upload.sharevideoclip.com' : 'http://dev.up.sharevideoclip.com';

//define variable
defined('WEB_ROOT') || define('WEB_ROOT', realpath(dirname(dirname(__FILE__))));
define('STATIC_URL', $staticURL);
define('BASE_URL', $baseURL);
define('UPLOAD_URL', $uploadURL);
define('PUBLIC_PATH', WEB_ROOT . '/public');
define('STATIC_PATH', WEB_ROOT . '/static');
define('UPLOAD_PATH', WEB_ROOT . '/uploads/');
define('CAPTCHA_PATH', UPLOAD_PATH . 'captcha/');
define('CAPTCHA_URL', UPLOAD_URL . 'captcha');
define('CONFIG_CACHE_DIR', WEB_ROOT . '/config/config-cache');
define('SES_EXPIRED', 7776000);
define('CLI_DEBUG', 0);
define('SEARCH_PREFIX', 'share_data_');
define('WORKER_PREFIX', 'share');
define('LOG_FOLDER', WEB_ROOT . '/logs');

// Setup autoloading
require 'init_autoloader.php';
// Run the application!
try {
    \Zend\Mvc\Application::init(require 'config/application.config.php')->run();
} catch (\Exception $exc) {
    die($exc->getMessage());
}