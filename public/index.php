<?php

/**
 *  Define application environment
 */
if (!empty($_SERVER['SERVER_NAME'])) {
    define('SERVER_NAME', $_SERVER['SERVER_NAME']);
} elseif (!empty($_ENV['SERVER_NAME'])) {
    define('SERVER_NAME', $_ENV['SERVER_NAME']);
} else {
    define('SERVER_NAME', 'development');
}
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', SERVER_NAME);


// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap()
            ->run();