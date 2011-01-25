<?php

/**
 * Patchwork_Controller_Plugin_HttpAuth
 *
 * @package    Patchwork
 * @subpackage Authorisation
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * Patchwork_Controller_Plugin_HttpAuth
 *
 * basic http authentciation against doctrine/database
 *
 * @package    Patchwork
 * @subpackage Authorisation
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_Controller_Plugin_Auth_HttpBasic
extends Patchwork_Controller_Plugin_Auth_Abstract
implements Patchwork_Controller_Plugin_Auth
{

    /**
     * resolver
     * @var Patchwork_Auth_Adapter_Http_Resolver_DB
     */
    protected $resolver;
    /**
     * module to protect
     * @var string
     */
    protected $_module;

    /**
     * 
     */
    public function _setConfigToOptions()
    {
        parent::_setConfigToOptions();
        if ($this->getContainer()->has('config')) {
            if (isset($this->getContainer()->config->patchwork->options->restAPI->module)) {
                $this->setModule(
                    $container->config->patchwork->options->restAPI->module
                );
            }
        }
    }

    /**
     * return the current role
     *
     *
     * @return string
     */
    public function getUserRole()
    {
        $role = Patchwork::ACL_GUEST_ROLE;
        $user = $this->resolver->getAuthModel();
        if ($user instanceof Zend_Acl_Role_Interface) {
            return $user->getRoleId();
        }
        /**
         * @todo check: remove dependency?
         */
        if (!$user || $role == '') {
            return Patchwork::ACL_GUEST_ROLE;
        }
    }

    /**
     * checks if controller name is an allowed resource, modifies the request
     * if not valid
     *
     * @param Zend_Controller_Request_Abstract $request request
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        if ($request->getModuleName() != $this->_module) {
            return;
        }

        $this->resolver = $this->getContainer()
                ->getInstance('Patchwork_Auth_Adapter_Http_Resolver_DB');
        assert($this->resolver instanceof Zend_Auth_Adapter_Http_Resolver_Interface);
        /** @todo adapter requires no dependencies, but getAdapter() better */
        $adapter = new Patchwork_Auth_Adapter_Http(
                array(
                    'accept_schemes' => 'basic',
                    'realm' => $this->_module
                )
        );
        $adapter->setBasicResolver($this->resolver);
        $storage = new Zend_Auth_Storage_NonPersistent;
        Zend_Auth::getInstance()->setStorage($storage);

        $response = Zend_Controller_Front::getInstance()->getResponse();
        assert($request instanceof Zend_Controller_Request_Http);
        assert($response instanceof Zend_Controller_Response_Http);

        $adapter->setRequest($request);
        $adapter->setResponse($response);

        $result = Zend_Auth::getInstance()->authenticate($adapter);
        if (!$result->isValid()) {
            return; /** @todo keep here until challenging is refactored */
        }

        if (!$this->isAllowedRequest($request)) {
            $controllerName = $this->getContainer()->getApplicationConfig()
                ->patchwork->options->acl->errorController;
            $actionName = $this->getContainer()->getApplicationConfig()
                ->patchwork->options->acl->errorAction;
            $request->setControllerName($controllerName);
            $request->setActionName($actionName);
        }
    }

    /**
     * check if acl allows module/controller/action
     * 
     * @param Zend_Controller_Request_Abstract $request
     * @return boolean
     */
    public function isAllowedRequest(Zend_Controller_Request_Abstract $request)
    {
        $resource = $this->_getResourceFromRequest($request);
        $privilege = $request->getActionName();
        return $this->acl->has($resource) &&
        $this->acl->isAllowed($this->getUserRole(), $resource, $privilege);
    }

    /**
     * set the module to provide basic auth for
     * 
     * @param string $module module name
     * @return self
     */
    public function setModule($module)
    {
        $this->_module = $module;
        return $this;
    }

}