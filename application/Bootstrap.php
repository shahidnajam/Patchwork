<?php
/**
 * Bootstrap class
 *
 * registers registry keys and starts Doctrine
 *
 * @package    Patchwork
 * @subpackage Bootstrap
 * @author     Daniel Pozzi
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * start autoloading
     *
     * @return Zend_Autoloader
     */
    protected function _initAppAutoload()
    {
        set_include_path(
            implode(
                PATH_SEPARATOR,
                array(
                    realpath(
                        APPLICATION_PATH. DIRECTORY_SEPARATOR . '..'
                        . DIRECTORY_SEPARATOR .'library'
                    ),
                    APPLICATION_PATH.'/models/generated'
                )
            )
        );
        
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('App_');
        $autoloader->registerNamespace('Base');
        $autoloader->registerNamespace('Patchwork');
        return $autoloader;
    }

    /**
     * start Zend_Navigation
     *
     * 
     */
    protected function _initNavigation()
    {
        Zend_Registry::set(
            Patchwork::NAVIGATION_REGISTRY_KEY,
            new Zend_Navigation(
                require(APPLICATION_PATH .'/configs/navigation.php')
            )
        );
    }

    /**
     * init view
     *
     * @return Zend_View
     */
    protected function _initView()
    {
        // Initialize view
        $view = new Zend_View();
        $view->doctype('XHTML1_STRICT');
        //$view->headTitle('PATCHWORK');
        $view->env = APPLICATION_ENV;

        // Add it to the ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
            'ViewRenderer'
        );
        $viewRenderer->setView($view);

        // Return it, so that it can be stored by the bootstrap
        return $view;
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
     * access control
     * 
     */
    public function _initAcl()
    {
        $acl = new Zend_Acl();
        # Konfig einlesen und in ein Array wandeln
        $config = new Zend_Config_Ini(APPLICATION_PATH . "/configs/acl.ini");
        $config = $config->toArray();

        # Die Ressourcen dem ACL Syste4m hinzugüen
        foreach ($config["res"] as $res) {
            $acl->add(new Zend_Acl_Resource($res));
        }

        # DIe Rollen dem ACL System hinzufügen
        foreach($config["role"] as $role) {
            $acl->addRole(new Zend_Acl_Role($role));

            # Wenn es zu der Rolle auch was in der Accesslist gibt, dann füge das hinzu
            if(isset($config["access"][$role])) {
                foreach($config["access"][$role] as $access) {
                    $acl->allow($role, $access);
                }
            }
        }
        Zend_Registry::set(Patchwork::ACL_REGISTRY_KEY, $acl);
    }

    /**
     * starting doctrine
     *
     *
     */
    public function _initDoctrine()
    {
        $this->bootstrap('AppAutoload')
             ->bootstrap('ConfigToRegistry');

        require 'Doctrine.php';
        $manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        /*$manager->setAttribute(
            Doctrine::ATTR_MODEL_LOADING,
            Doctrine::MODEL_LOADING_CONSERVATIVE
        );*/
        $manager->setAttribute(Doctrine::ATTR_AUTOLOAD_TABLE_CLASSES, true);

        $config =  Zend_Registry::get('config');

        Doctrine::loadModels($config->doctrine->options->models_path);
        Doctrine::loadModels($config->doctrine->options->models_path . '/generated');


        $conn = Doctrine_Manager::connection(
            $config->doctrine->connections->db, 'doctrine'
        );
        $conn->setAttribute(Doctrine::ATTR_USE_NATIVE_ENUM, true);
        
        return $conn;
    }
}
