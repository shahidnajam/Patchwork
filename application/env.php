<?php
/**
 * Script to define some environment constants and include paths.
 *
 * @author     Daniel Pozzi
 * @package    Application
 * @subpackage Bootstrap
 */

/**
 * show all errors
 */
error_reporting(E_ALL);

/**
 *  Define path to application directory
 */
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__)));
defined('CONFIG_PATH')
    || define('CONFIG_PATH', APPLICATION_PATH . DIRECTORY_SEPARATOR . 'configs');
defined('LIBRARY_PATH')
    || define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/../library'));
defined('TMP_PATH')
    || define('TMP_PATH', realpath(dirname(__FILE__) . '/../tmp'));
defined('MODELS_PATH')
    || define('MODELS_PATH', realpath(dirname(__FILE__) . '/models/generated'));

/**
 *  Define application environment
 */
if (!empty($_SERVER['SERVER_TYPE'])) {
    define('SERVER_TYPE', $_SERVER['SERVER_TYPE']);
} elseif (!empty($_ENV['SERVER_TYPE'])) {
    define('SERVER_TYPE', $_ENV['SERVER_TYPE']);
} else {
    define('SERVER_TYPE', 'development');
}
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', SERVER_TYPE);

/**
 * set proper include paths for Zend and Doctrine
 */
set_include_path(
    implode(
        PATH_SEPARATOR, array(
            LIBRARY_PATH,
            LIBRARY_PATH . DIRECTORY_SEPARATOR . 'vendor/sfYaml',
            MODELS_PATH,
            get_include_path()
        )
    )
);
