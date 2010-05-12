#!/usr/bin/env php
<?php

if (!empty($_SERVER['SERVER_TYPE'])) {
    define('SERVER_TYPE', $_SERVER['SERVER_TYPE']);
} elseif (!empty($_ENV['SERVER_TYPE'])) {
    define('SERVER_TYPE', $_ENV['SERVER_TYPE']);
} else {
    define('SERVER_TYPE', 'development');
}

define('ROOT_PATH', dirname(dirname(__FILE__)));
define('LIBRARIES_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'library');
define('DS', DIRECTORY_SEPARATOR);

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
define('APPLICATION_ENV', SERVER_TYPE);

// Ensure library/ is on include_path
set_include_path(
    implode(
        PATH_SEPARATOR,
        array(
            realpath(LIBRARIES_PATH),
            realpath(LIBRARIES_PATH . DIRECTORY_SEPARATOR . 'Doctrine' . DIRECTORY_SEPARATOR . 'Parser' . DIRECTORY_SEPARATOR . 'sfYaml'),
            get_include_path(),
        )
    )
);
require_once 'sfYaml.php';
/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);


$application->getBootstrap()->bootstrap('Doctrine');
$resource = $application->getBootstrap()->getResource('Doctrine');

$config = new Zend_Config_Ini(
    APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR
        . 'application.ini',
    APPLICATION_ENV
);

Doctrine_Manager::connection($config->doctrine->connections->db);
$doctrineConfig = $config->doctrine->options->toArray();
 
$cli = new Doctrine_Cli($doctrineConfig);
$cli->run($_SERVER['argv']);