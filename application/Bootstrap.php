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

    /**
     * start Zend_Navigation, register it in registry
     *
     * 
     */
    protected function _initNavigation()
    {
        Zend_Registry::set(
            Patchwork::NAVIGATION_REGISTRY_KEY,
            new Zend_Navigation(
                require(CONFIG_PATH . DIRECTORY_SEPARATOR . 'navigation.php')
            )
        );
    }

    /**
     * make config available in registry
     *
     * 
     */
    public function _initConfigToRegistry()
    {
        Zend_Registry::set(
                Patchwork::CONFIG_REGISTRY_KEY,
                new Zend_Config($this->getOptions())
        );
    }

    /**
     * access control, registerd in registry for navigation
     *
     * 
     * 
     */
    public function _initAcl()
    {
        $acl = new Zend_Acl();
        $config = new Zend_Config_Ini(APPLICATION_PATH . "/configs/acl.ini");
        $config = $config->toArray();

        foreach ($config["res"] as $res) {
            $acl->add(new Zend_Acl_Resource($res));
        }

        foreach ($config["role"] as $role) {
            $acl->addRole(new Zend_Acl_Role($role));

            if (isset($config["access"][$role])) {
                foreach ($config["access"][$role] as $access) {
                    $acl->allow($role, $access);
                }
            }
        }
        Zend_Registry::set(Patchwork::ACL_REGISTRY_KEY, $acl);
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
        $this->_initConfigToRegistry();
    }
}
