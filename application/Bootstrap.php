<?php

/**
 * Bootstrap class
 *
 * registers registry keys and starts Doctrine
 *
 * @package    Application
 * @subpackage Bootstrap
 * @author     Daniel Pozzi
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     * start autoloading
     *
     * @return Zend_Loader_Autoloader
     */
    protected function _initAppAutoload()
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->setFallbackAutoloader(true);

        $mal = new Zend_Application_Module_Autoloader(array(
                'namespace' => 'App',
                'basePath' => dirname(__FILE__),
            ));

        return $mal;
    }

    protected function _initConfigToRegistry()
    {
        Zend_Registry::set(
                Patchwork::CONFIG_REGISTRY_KEY,
                new Zend_Config($this->getOptions())
        );
    }

    /**
     * start Zend_Navigation
     *
     * @return Zend_Navigation
     */
    protected function _initNavigation()
    {
        return new Zend_Navigation(
            require(CONFIG_PATH . DIRECTORY_SEPARATOR . 'navigation.php')
        );
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

        // Return it, so that it can be stored by the bootstrap
        return $locale;
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
