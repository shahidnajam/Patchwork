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
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     * This method ensures that the dependency injection container is
     * initialized before any other component.
     *
     * @param  null|string|array $resource
     * @return void
     * @throws Zend_Application_Bootstrap_Exception
     */
    protected function _bootstrap($resource = null)
    {
        if ($this->_container === null) {
            /** Create the container and register it */
            $this->setContainer(new Patchwork_Container($this->getOptions()));

            /** Ensure we bootstrap the configuration into the container now */
            $this->_executeResource('config');
            /** Ensure we bootstrap the Front Controller too */
            $this->_executeResource('config');
        }

        /** Remove underscores */
        if (!empty($resource)) {
            $resource = strtr(
                    $resource,
                    array(
                        '_' => ''
                    )
            );
        }

        parent::_bootstrap($resource);
    }

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
     * store "config" in container
     * @return Zend_Config
     */
    protected function _initConfig()
    {
        return new Zend_Config($this->getOptions());
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
