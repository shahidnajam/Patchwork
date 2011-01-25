<?php
/**
 * Patchwork
 *
 * @category    Library
 * @package     Patchwork
 * @subpackage  Plugin
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * Firewall plugin
 *
 * @category    Library
 * @package     Patchwork
 * @subpackage  Plugin
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_Controller_Plugin_Firewall extends Zend_Controller_Plugin_Abstract
{
    /**
     * front controller
     * @var Zend_Controller_Front
     */
    protected $front;

    /**
     * GET params
     * @var array
     */
    public $get = array();
    /**
     * POST params
     * @var array
     */
    public $post = array();
    /**
     * @var array
     */
    protected $_ignored = array('module', 'controller', 'action');

    /**
     * Constructor
     *
     * @param Zend_Controller_Front $front front controller
     */
    public function  __construct(Zend_Controller_Front $front)
    {
        $this->front = $front;
    }

    /**
     * fire on dispatch loop startup
     * 
     * @param Zend_Controller_Request_Http $request request
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $controller = $this->front->getDispatcher()
            ->getControllerClass($request);
        $action = $this->front->getDispatcher()
            ->getActionMethod($request);
        $method = strtolower($request->getServer('REQUEST_METHOD'));
        
        $class = $this->front->getDispatcher()->loadClass($controller);
        $ref = new ReflectionClass($controller);
        $refMethod = $ref->getMethod($action);
        $this->parseDocCommentToRules($refMethod->getDocComment(), $method);

        /**
         * must-haves
         */
        foreach ($request->getParams() as $key => $value) {
            if(in_array($key, $this->_ignored)) {
                continue;
            }
            
            if($this->isRegisteredParam($key, $method)) {
                $this->validateParam($key,$value);
            } else {
                throw new Exception('Unregistered request param ' . $key);
            }
        }
    }

    /**
     * parses a functions comments for tags beginning with get,post,request
     *
     * @param string $docComment doc block
     * @param string $method     method name (get, post, request)
     */
    protected function parseDocCommentToRules($docComment, $method)
    {
        $entries = explode('@', $docComment);
        unset($entries[0]);
        foreach ($entries as $entry) {
            if(substr($entry, 0, strlen($method)) == $method) {
                $json = substr($entry, strlen($method));
                $this->addRule(new Patchwork_Controller_Plugin_Firewall_Rule($json));
            }
        }
    }

    /**
     * check if param has been registered
     *
     * @param string $param
     * @param string $method
     *
     * @return boolean
     */
    protected function isRegisteredParam ($param, $method)
    {
        return in_array($param, $this->$method);
    }

    /**
     * add a rule
     *
     * @param Patchwork_Controller_Plugin_Firewall_Rule $rule rule
     * @return self
     */
    public function addRule(Patchwork_Controller_Plugin_Firewall_Rule $rule)
    {
        $this->_params[$rule->name] = $rule;
        return $this;
    }
}