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
     * Constructor
     * 
     * @param Patchwork_Container $container di container
     */
    public function __construct(Patchwork_Container $container)
    {
        $this->_container = $container;
        $this->_setConfigToOptions();
    }

    /**
     * return the current role
     *
     * @return string
     */
    public static function getUserRole()
    {
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            return Patchwork::ACL_GUEST_ROLE;
        }

        $id = $auth->getIdentity();
        if (is_object($id) && $id instanceof Zend_Acl_Role_Interface)
            return $id->getRoleId();
        elseif (isset($id['role']))
            return $id['role'];
        else
            return Patchwork::ACL_GUEST_ROLE;
    }

    /**
     * reads config options from the config stored in the container
     * 
     * @throws Patchwork_Exception
     */
    protected function _setConfigToOptions()
    {
        if($this->_container->has('config') 
            && $this->_container->config->patchwork->options->acl
        ){
            array_merge(
                $this->_options,
                $this->_container->config->patchwork->options->acl->toArray()
            );
        }
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
        if (!$this->isAllowedRequest(self::getUserRole(), $request)) {
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
    public function redirectToAccessDenied(
        Zend_Controller_Request_Abstract $request
    ){
        $request->setModuleName($this->_options['errorModule'])
            ->setControllerName($this->_options['errorController'])
            ->setActionName($this->_options['errorAction']);
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
        $acl = $this->_container->acl;
        if (!$acl->hasRole($role)) {
            return false;
        }

        $resource = $request->getModuleName() . self::MODULE_CONTROLLER_GLUE .
            $request->getControllerName();
        if (!$acl->has($resource)) {
            return false;
        }

        if($acl->isAllowed($role, $resource, $request->getActionName())) {
            return true;
        }

        return false;
    }
}