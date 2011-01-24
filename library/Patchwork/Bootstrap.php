<?php
/**
 * Bootstrap which invokes Patchwork_Container
 *
 * @author     Daniel Pozzi
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

        return $mal;
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