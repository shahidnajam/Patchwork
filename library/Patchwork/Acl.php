<?php

/**
 * Patchwork
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage Acl
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * Patchwork_Acl
 *
 * An ini-based Acl mapped to controllers as resources and actions as privileges.
 * The factory loads a config from the module directory
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage Acl
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_Acl extends Zend_Acl
{
    /**
     * where the config is stored: patchwork.options.acl
     */
    const APPLICATION_CONFIG_ACL_KEY = 'acl';
    /**
     * default role
     */
    const GUEST_ROLE = 'guest';

    /**
     * factory methods
     *
     * @param string $role    role
     * @param string $iniFile location of the ini file to use
     * 
     * @return Patchwork_Acl
     */
    public static function factory(
        $role,
        Zend_Controller_Request_Abstract $request
    ) {
        $moduleDir = Zend_Controller_Front::getInstance()->getModuleDirectory();
        $iniFile = $moduleDir . '/configs/acl.ini';
        $config = new Patchwork_Acl_Ini($iniFile, $role);
        return new self($config);
    }

    /**
     * instantiate with an ini file
     * 
     * @param Patchwork_Acl_Ini $config configuration
     */
    public function __construct(Patchwork_Acl_Ini $config)
    {
        $parentRole = ($config->getExtendedRole()!='')?
            $config->getExtendedRole():null;
        if($parentRole){
            $this->addRole($parentRole);
        }
        $role = $config->getSectionName();
        $this->addRole($role, $parentRole);
        foreach ($config as $module => $controllers) {
            if($module == '_extends'){
                continue;
            }
            $this->addResource(new Zend_Acl_Resource($module));
            foreach ($controllers as $controller => $true) {
                $this->allow($role, $module, $controller);
            }
        }
    }

    /**
     * registers itself in Zend_Registry under Patchwork::ACL_REGISTRY_KEY
     * 
     * @return Patchwork_Acl
     */
    public function registerInRegistry()
    {
        Zend_Registry::set(Patchwork::ACL_REGISTRY_KEY, $this);
        return $this;
    }

    /**
     * check if a request is allowed
     *
     * @param string                           $role    role
     * @param Zend_Controller_Request_Abstract $request request
     * 
     * @return boolean
     */
    public function isAllowedRequest(
        $role,
        Zend_Controller_Request_Abstract $request
    ) {
        if(!$this->hasRole($role)){
            return false;
        }
        
        if(!$this->has($request->getControllerName())){
            return false;
        }

        if($this->isAllowed(
            $role,
            $request->getControllerName(),
            $request->getActionName()
        )){
            return true;
        }

        return false;
    }
}