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
 * The factory loads acl.ini form each module directory
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
     * concats module and controller into a resource
     */
    const MODULE_CONTROLLER_GLUE = '_';

    /**
     * factory method
     *
     * @param Zend_Controller_Request_Abstract $request request
     * 
     * @return Patchwork_Acl
     */
    public static function factory()
    {
        $staticPath = '/configs/acl.ini';
        $acl = new self();
        $defaultIni = APPLICATION_PATH . $staticPath;
        if(is_file($defaultIni)) {
            $acl->addConfig(new Patchwork_Acl_Ini($defaultIni));
        }

        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules';
        $iter = new DirectoryIterator($path);
        foreach ($iter as $moduleDir) {
            
            if (!$moduleDir->isDir() || $moduleDir->isDot()) {
                 continue;
            }
            $iniFile = APPLICATION_PATH . '/modules/' . $moduleDir . $staticPath;
            if(!is_file($iniFile)){
                continue;
            }
            
            $config = new Patchwork_Acl_Ini($iniFile);
            $acl->addConfig($config);
        }

        return $acl;
    }

    /**
     * add a configuration
     * 
     * @param Patchwork_Acl_Ini $config configuration
     * @return self
     */
    public function addConfig(Patchwork_Acl_Ini $config)
    {
        $config->addConfigToAcl($this);
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
        $role, Zend_Controller_Request_Abstract $request
    )
    {
        if (!$this->hasRole($role)) {
            return false;
        }

        $resource = $request->getModuleName() . self::MODULE_CONTROLLER_GLUE .
            $request->getControllerName();
        if (!$this->has($resource)) {
            return false;
        }

        if($this->isAllowed(
            $role,
            $resource,
            $request->getActionName()
        )) {
            return true;
        }

        return false;
    }

}