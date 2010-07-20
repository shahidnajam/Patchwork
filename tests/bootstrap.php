<?php
/**
 * PHPUnit bootstrapper
 *
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 * @package    Testing
 * @subpackage Bootstrap
 */

// Define application environment
define('APPLICATION_ENV', 'testing');

require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'application'
    . DIRECTORY_SEPARATOR . 'env.php';

/** Zend_Application */
require_once 'Zend/Application.php';

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();