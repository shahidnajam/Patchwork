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
 * registers registry keys and starts Doctrine
 *
 * @package    Application
 * @subpackage Default
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
class Bootstrap extends Patchwork_Bootstrap
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
     * command line interface bootstrapping
     *
     * 
     */
    public function _initCli()
    {
        $this->_initAppAutoload();
    }

}
