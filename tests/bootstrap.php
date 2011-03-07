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
define('APPLICATION_PATH', dirname(dirname(__FILE__)) . '/application');

set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';
require_once 'vendor/sfYaml/sfYaml.php';

$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();

require_once 'ControllerTestCase.php';
