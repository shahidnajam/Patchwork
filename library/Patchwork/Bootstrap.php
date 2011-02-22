<?php

/**
 * Bootstrap which invokes Patchwork_Container
 * - inject the application ini config into the container
 * - registers the error handler to throw ErrorExceptions at any time
 *
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 * @package    Patchwork
 * @subpackage Bootstrap
 */
class Patchwork_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
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
     * store "config" in container, container->getApplicationConfig()
     * 
     * @return Zend_Config
     */
    protected function _initConfig()
    {
        return new Zend_Config($this->getOptions());
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

        /*
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
        ));
*/
        return $mal;
    }

    /**
     * registers an error handler that turns every error into an exception
     * 
     * DO NOT CHANGE THIS! BETTER CODE WITHOUT ERRORS.
     *
     * If you really need to evade, you can unregister the error handler
     * temporarily
     *
     * @see Patchwork_Error_Handler
     */
    protected function _initErrorHandler()
    {
        $this->getContainer()
            ->getInstance('Patchwork_Error_Handler')
            ->registerAsErrorHandler();
    }

    /**
     * the container instance
     * 
     * @return Patchwork_Container
     */
    public function getContainer()
    {
        return $this->_container;
    }

}