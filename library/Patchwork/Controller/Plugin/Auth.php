<?php

/**
 * Patchwork
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage Authorisation
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * Patchwork_Controller_Plugin_Auth
 *
 * checks if the role is allowed to access controller->action
 * requires "Zend_Acl" to be registered in registry
 * the Zend_Auth identity should implement Zend_Acl_Role_Interface
 *
 * @package    Patchwork
 * @subpackage Authorisation
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
    /**
     * return the current role
     *
     * @return string
     */
    public static function getUserRole()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity())
            return Patchwork_Acl::GUEST_ROLE;

        $id = $auth->getIdentity();
        if (is_object($id) && $id instanceof Zend_Acl_Role_Interface)
            return $id->getRoleId();
        elseif (isset($id['role']))
            return $id['role'];
        else
            return Patchwork_Acl::GUEST_ROLE;
    }

    /**
     *
     * @return Zend_Config
     * @throws Patchwork_Exception
     */
    protected function _getConfig()
    {
        $config = Zend_Registry::get(Patchwork::CONFIG_REGISTRY_KEY);
        if(isset($config->patchwork->options->acl))
            return $config->patchwork->options->acl;
        else
            throw Patchwork_Exception::missingAclConfig();
    }

    /**
     * get the patchwork acl
     * 
     * @return Patchwork_Acl
     */
    protected function _getACL()
    {
        if (!Zend_Registry::isRegistered(Patchwork::ACL_REGISTRY_KEY)) {
            $config = $this->_getConfig();
            if (isset($config->autoload) && $config->autoload) {
                $acl = Patchwork_Acl::factory(self::getUserRole())
                    ->registerInRegistry();
            } else {
                throw Patchwork_Exception::ACLnotRegistered();
            }
        } else {
            $acl = Zend_Registry::get(Patchwork::ACL_REGISTRY_KEY);
            if(!$acl instanceof Patchwork_Acl){
                throw Patchwork_Exception::wrongACLType();
            }
        }

        return $acl;
    }

    /**
     * checks if controller name is an allowed resource, modifies the request
     * if not valid
     * 
     * @param Zend_Controller_Request_Abstract $request request
     * @throws Patchwork_Exception
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $acl = $this->_getACL();

        if (!$acl->isAllowedRequest(self::getUserRole(), $request)) {
            $this->redirectToAccessDenied($request);
        }
    }

    /**
     * Checks permission using ACL
     *
     * @param Zend_Controller_Request_Abstract $request request
     *
     * @return void
     */
    public function redirectToAccessDenied(Zend_Controller_Request_Abstract $request)
    {
        $config = $this->_getConfig();
        
        $request->setControllerName($config->errorController);
        $request->setActionName($config->errorAction);
    }

}