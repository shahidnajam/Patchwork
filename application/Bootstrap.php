<?php

/**
 * Patchwork
 *
 * @category    Application
 * @package     Default
 * @subpackage  Bootstrap
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */
/**
 * Bootstrap class
 *
 * @package    Application
 * @subpackage Default
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
require_once APPLICATION_PATH . '/../library/Patchwork/Bootstrap.php';

class Bootstrap extends Patchwork_Bootstrap
{

    public function _initModuleAutoloading()
    {
        $mal = new Zend_Application_Module_Autoloader(array(
                'namespace' => 'App',
                'basePath' => dirname(__FILE__),
            ));

        $loader = new Zend_Application_Module_Autoloader(array(
                'namespace' => 'Core',
                'basePath' => APPLICATION_PATH . '/modules/core',
            ));

        $loader->addResourceTypes(array(
            'model' => array(
                'path' => 'models',
                'namespace' => 'Model',
            ),
            'form' => array(
                'path' => 'forms',
                'namespace' => 'Form',
            ),
            'service' => array(
                'path' => 'services',
                'namespace' => 'Service',
            ),
        ));

        $uloader = new Zend_Application_Module_Autoloader(array(
                'namespace' => 'User',
                'basePath' => APPLICATION_PATH . '/modules/user',
            ));

        $uloader->addResourceTypes(array(
            'model' => array(
                'path' => 'models',
                'namespace' => 'Model',
            ),
            'form' => array(
                'path' => 'forms',
                'namespace' => 'Form',
            ),
            'service' => array(
                'path' => 'services',
                'namespace' => 'Service',
            ),
        ));


        return $mal;
    }

    /**
     * init app-wide locale
     *
     * @return Zend_Locale
     */
    public function _initLocale()
    {
        $locale = new Zend_Locale();
        Zend_Registry::set('Zend_Locale', $locale);

        // Return it, so that it can be stored in the container
        return $locale;
    }

    /**
     * invokes the navigation
     * 
     * @return Zend_Navigation
     */
    public function _initNavigation()
    {
        $navigation = new Zend_Navigation(
                include APPLICATION_PATH . '/configs/navigation.php'
        );
        Zend_Registry::set('Zend_Navigation', $navigation);

        return $navigation;
    }

    /**
     * command line interface bootstrapping
     *
     *
     */
    public function _initCli()
    {
        $this->_initAppAutoload();
    }

}

