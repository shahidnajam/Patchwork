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
    || define('MODELS_PATH', realpath(dirname(__FILE__) . '/models'));
defined('ROOT_PATH')
    || define('ROOT_PATH', realpath(dirname(dirname(__FILE__))));

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

/**
 * set proper include paths for Zend and Doctrine
 */
set_include_path(
    implode(
        PATH_SEPARATOR, array(
            LIBRARY_PATH,
            LIBRARY_PATH . DIRECTORY_SEPARATOR . 'vendor/sfYaml',
            get_include_path()
        )
    )
);
