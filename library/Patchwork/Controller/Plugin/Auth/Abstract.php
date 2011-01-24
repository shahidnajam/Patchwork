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
 * Patchwork_Controller_Plugin_Auth_Abstract
 *
 * checks if the role is allowed to access controller->action
 * requires "Zend_Acl" to be registered in registry
 * the Zend_Auth identity should implement Zend_Acl_Role_Interface
 *
 * @package    Patchwork
 * @subpackage Authorisation
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
abstract class Patchwork_Controller_Plugin_Auth_Abstract
extends Zend_Controller_Plugin_Abstract
{
    /**
     * concats module and controller into a resource
     */
    const MODULE_CONTROLLER_GLUE = '_';
    const MODULE_ONLY = 'module';
    const CONTROLLER_ONLY = 'controller';
    
    /**
     *
     * @var Zend_Acl
     */
    protected $acl;

    /**
     * di container
     * @var Patchwork_Container
     */
    protected $container;

    /**
     * identifier how the acl resource is detected
     * (module = resource, controller, both)
     * @var string
     */
    protected $_resourceBuildingMethod = self::MODULE_CONTROLLER_GLUE;
    
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
     * @param Zend_Controller_Request_Abstract $request request
     * @param Zend_Acl                         $acl     acl
     */
    public function  __construct(
        Zend_Acl $acl,
        Patchwork_Container $container
    ) {
        $this->acl = $acl;
        $this->container = $container;
    }

    /**
     * get the container
     * 
     * @return Patchwork_Container
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * reads config options from the config stored in the container
     * 
     * @throws Patchwork_Exception
     */
    protected function _setConfigToOptions()
    {
        if($this->container->has('config') 
            && $this->container->config->patchwork->options->acl
        ){
            array_merge(
                $this->_options,
                $this->container->config->patchwork->options->acl->toArray()
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
        if (!$this->isAllowedRequest($request)) {
            $this->_redirectToAccessDenied($request);
        }
    }

    /**
     * Checks permission using ACL
     *
     * @param Zend_Controller_Request_Abstract $request request
     *
     * @return void
     */
    protected function _redirectToAccessDenied(
        Zend_Controller_Request_Abstract $request
    ){
        $request->setModuleName($this->_options['errorModule'])
            ->setControllerName($this->_options['errorController'])
            ->setActionName($this->_options['errorAction']);
    }

    /**
     * set the resource string building method
     * 
     * @param string $method
     */
    public function setResourceBuildingMethod($method)
    {
        $allowedMethods = array(
            self::MODULE_ONLY,
            self::CONTROLLER_ONLY,
            self::MODULE_CONTROLLER_GLUE
        );
        if(!in_array($method, $allowedMethods)) {
            throw new InvalidArgumentException(
                'Invalid resource building method '. $method
            );
        }

        $this->_resourceBuildingMethod = $method;
        return $this;
        
    }
    /**
     * get the resource name
     * 
     * @param Zend_Controller_Request_Abstract $request request
     * @return string
     * @throws RuntimeException
     */
    protected function _getResourceFromRequest(
        Zend_Controller_Request_Abstract $request
    ){
        switch ($this->_resourceBuildingMethod) {
            case self::MODULE_ONLY:
                return $request->getModuleName();
            case self::CONTROLLER_ONLY:
                return $request->getControllerName();
            case self::MODULE_CONTROLLER_GLUE:
                return $request->getModuleName()
                . self::MODULE_CONTROLLER_GLUE .
                $request->getControllerName();
            default:
                throw new RuntimeException('Could not determine acl resource');
        }
    }
}