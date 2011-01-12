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
class Patchwork_Controller_Plugin_Auth_HttpBasic extends Patchwork_Controller_Plugin_Auth_Abstract
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
        $user = $this->resolver->getAuthModel();
        if ($user instanceof Zend_Acl_Role_Interface) {
            $role = $user->getRoleId();
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

        $resource = $request->getModuleName() .
            '_' . $request->getControllerName();
        $privilege = $request->getActionName();
        if (
            !$this->acl->has($resource) ||
            !$this->acl->isAllowed($this->getUserRole(), $resource, $privilege)
        ) {
            //throw new Exception($resource.' '.$this->getUserRole());
            $request->setControllerName('index');
            $request->setActionName('denied');
        }
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