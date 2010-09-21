<?php
/**
 * Patchwork_Controller_Plugin_Auth
 *
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
     * guest role name
     */
    const GUEST_ROLE = 'guest';

    const ERROR_CONTROLLER = 'error';
    const ERROR_ACTION     = 'denied';

    /**
     * return the current role
     *
     *
     * @return string
     */
    public static function getUserRole()
    {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity())
            return self::GUEST_ROLE;

        $id = $auth->getIdentity();
        if(is_object($id) && $id instanceof Zend_Acl_Role_Interface)
            return $id->getRoleId();
        elseif(isset($id['role']))
            return $id['role'];
        else
            return self::GUEST_ROLE;
    }

    /**
     * checks if controller name is an allowed resource, modifies the request
     * if not valid
     * 
     * @param Zend_Controller_Request_Abstract $request request
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        if(!Zend_Registry::isRegistered(Patchwork::ACL_REGISTRY_KEY))
            throw new Patchwork_Exception(
                'ACL not found in registry under '. Patchwork::ACL_REGISTRY_KEY
            );

        $acl = Zend_Registry::get(Patchwork::ACL_REGISTRY_KEY);
        $resource = $request->getControllerName();
        if(
            !$acl->has($resource) ||
            !$acl->isAllowed(self::getUserRole(), $resource)
        ) {
            $this->redirectToAccessDenied($request);
        }
    }

    /**
     * Checks permission using ACL
     *
     * @param Zend_Controller_Request_Abstract $request
     *
     * @return void
     */
    public function redirectToAccessDenied(Zend_Controller_Request_Abstract $request)
    {
        $request->setControllerName('error');
        $request->setActionName('denied');
    }
}