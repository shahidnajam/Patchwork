<?php

/**
 * Patchwork
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage Plugins
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
 * @subpackage Plugins
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_Controller_Plugin_Auth_Basic
extends Patchwork_Controller_Plugin_Auth_Abstract
implements Patchwork_Controller_Plugin_Auth
{
    /**
     * concats module and controller into a resource
     */
    const MODULE_CONTROLLER_GLUE = '_';

    /**
     * di container
     * @var Patchwork_Container
     */
    protected $_container;
    /**
     * options for access denied redirection target
     * @var array
     */
    protected $_options = array(
        'errorModule' => 'index',
        'errorController' => 'error',
        'errorAction' => 'accessdenied',
    );

    /**
     * return the current role
     *
     * @return string
     */
    public function getUserRole()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            return Patchwork::ACL_GUEST_ROLE;
        }

        $id = $auth->getIdentity();
        if (is_object($id) && $id instanceof Zend_Acl_Role_Interface) {
            return $id->getRoleId();
        } elseif (is_object($id) && property_exists($id, 'role')) {
            return $id->role;
        }
        elseif (isset($id['role'])) {
            return $id['role'];
        } else {
            return Patchwork::ACL_GUEST_ROLE;
        }
    }

    /**
     * check if a request is allowed
     *
     * @param string                           $role    role
     * @param Zend_Controller_Request_Abstract $request request
     *
     * @return boolean
     */
    public function isAllowedRequest(Zend_Controller_Request_Abstract $request)
    {
        $role = $this->getUserRole();
        if (!$this->acl->hasRole($role)) {
            return false;
        }

        $resource = $this->_getResourceFromRequest($request);
        if (!$this->acl->has($resource)) {
            return false;
        }

        if($this->acl->isAllowed($role, $resource, $request->getActionName())) {
            return true;
        }

        return false;
    }


}