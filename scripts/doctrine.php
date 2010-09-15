#!/usr/bin/env php
<?php

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'application'
    . DIRECTORY_SEPARATOR . 'env.php';

require_once 'sfYaml.php';
/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    CONFIG_PATH . DIRECTORY_SEPARATOR . 'application.ini'
);

$application->getBootstrap()->bootstrap('Doctrine');
$resource = $application->getBootstrap()->getResource('Doctrine');

$config = new Zend_Config_Ini(
    CONFIG_PATH . DIRECTORY_SEPARATOR . 'application.ini',
    APPLICATION_ENV
);

Doctrine_Manager::connection($config->resources->doctrine->connections->db);
$doctrineConfig = $config->resources->doctrine->options->toArray();
 
$cli = new Doctrine_Cli($doctrineConfig);
$cli->run($_SERVER['argv']);