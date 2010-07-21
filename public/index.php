<?php
/**
 * Public index file
 *
 * @author     Daniel Pozzi
 * @package    Application
 * @subpackage Bootstrap
 */

/** Environment */
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'application'
. DIRECTORY_SEPARATOR . 'env.php';

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    CONFIG_PATH . DIRECTORY_SEPARATOR . 'application.ini'
);
$application->bootstrap()
            ->run();

Zend_Session::writeClose(true);