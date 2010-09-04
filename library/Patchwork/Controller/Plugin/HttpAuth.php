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
class Patchwork_Controller_Plugin_HttpAuth
extends Zend_Controller_Plugin_Abstract
{
    const GUEST_ROLE = 'guest';

    /**
     * resolver
     * @var Patchwork_Auth_Adapter_Http_Resolver_Doctrine
     */
    public $resolver;

    /**
     * module to protect
     * @var string
     */
    protected $_module;
    
    /**
     * constructor
     *
     * @param string $module module name
     * @return self
     */
    public function  __construct($module)
    {
        $this->_module = $module;
    }

    /**
     * return the current role
     *
     *
     * @return string
     */
    public function getUserRole()
    {
        $user = $this->resolver->user;
        if ($user)
            $role = $user->role;

        if(!$user || $role == '')
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
        if($request->getModuleName() != $this->_module)
            return;
        
        $this->resolver = new Patchwork_Auth_Adapter_Http_Resolver_Doctrine;

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

        /**
         * standard acl stuff
         */
        if (!Zend_Registry::isRegistered(Patchwork::ACL_REGISTRY_KEY))
            Patchwork_Exception::ACLnotRegistered();

        $acl = Zend_Registry::get(Patchwork::ACL_REGISTRY_KEY);
        $resource = $request->getControllerName();
        if (
            !$acl->has($resource) ||
            !$acl->isAllowed($this->getUserRole(), $resource)
        ) {
            $request->setControllerName('index');
            $request->setActionName('denied');
        }
    }
}