<?php
/**
 * Patchwork
 *
 * @category    Library
 * @package     Patchwork
 * @subpackage  Container
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * Patchwork_Container
 *
 * Dependency Injection container for Patchwork. Tries to resolve dependencies
 * on its on, inspired by Horde's Injector
 *
 * @category    Library
 * @package     Patchwork
 * @subpackage  Container
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_Container
{
    /**
     * interface bindings
     * @var array
     */
    protected $_bindings = array();
    /**
     * factories for implementations, assoc, key is class name
     * @var array
     */
    protected $_factories = array();

    /**
     * retrieve an instance
     *
     * @param string $name class or interface name
     *
     * @return object
     */
    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        $resource = strtolower($name);
        if (isset($this->$resource)) {
            return $this->$resource;
        }

        return $this->createInstance($name);
    }

    /**
     * store an implementation under a name (similar to Zend_Registry::set)
     *
     * @param string $name  name to store implementation under
     * @param object $class implementation
     *
     * @return Patchwork_Container
     */
    public function __set($name, $class)
    {
        if(!is_object($class)){
            throw new InvalidArgumentException(
                'Second argument must be an object'
            );
        }
        $this->$name = $class;
        return $this;
    }

    /**
     *
     * @see __set
     */
    public function set($name, $class)
    {
        return $this->__set($name, $class);
    }

    /**
     * create a new instance
     *
     * @param string $name class or interface name
     *
     * @return object $name
     */
    public function createInstance($name)
    {
        /**
         * interface binding present?
         */
        if (array_key_exists($name, $this->_bindings)) {
            return $this->_createInstanceFromBinding($name);
        }

        /**
         * factory?
         */
        if (array_key_exists($name, $this->_factories)) {
            return $this->_createInstanceFromFactory($name);
        }

        $instance = $this->_createInstance($name);
        self::set($name, $instance);
        return $instance;
    }

    /**
     *
     * @param string $name interface name
     *
     * @return object instance of $name
     */
    protected function _createInstanceFromBinding($name)
    {
        $className = $this->_bindings[$name];
        $instance = $this->_createInstance($name);
        self::set($name, $instance);
        return $instance;
    }

    /**
     *
     * @param <type> $name
     *
     * @return object
     */
    protected function _createInstanceFromFactory($name)
    {
        list($factory, $method) = $this->_factories[$name];

        $factoryInstance = $this->_createInstance($factory);
        $instance = $factoryInstance->$method();

        self::set($name, $instance);
        return $instance;
    }

    /**
     *
     * @param string $name
     *
     * @return object
     */
    protected function _createInstance($name)
    {
        $ref = new ReflectionClass($name);

        $params = $ref->getConstructor()->getParameters();
        $args = array();
        foreach ($params as $param) {
            $reqClass = $param->getClass();
            if (class_exists($reqClass) || interface_exists($reqClass)) {
                $args[] = $this->getInstance($reqClass);
            } else {
                $args[] = null;
            }
        }

        $class = call_user_func_array('new ' . $name, $args);
        return $class;
    }

    /**
     * set an implementation binding
     *
     * @param string $interface interface
     * @param string $class     implementation class names
     *
     * @return Patchwork_Container
     */
    public function bindImplementation($interface, $class)
    {
        $this->_bindings[$interface] = $class;
        return $this;
    }

    /**
     * bind a factory to get an instance from
     *
     * @param string $name
     * @param string $factory
     * @param string $method
     *
     * @return Patchwork_Container
     */
    public function bindFactory($name, $factory, $method)
    {
        $this->_factories[$name] = array($factory, $method);
        return $this;
    }

    /**
     * get the currently used container
     * 
     * @return Patchwork_Container|Zend_Registry
     */
    public static function getBootstrapContainer()
    {
        return Zend_Controller_Front::getInstance()
            ->getParam('bootstrap')
            ->getContainer();
    }
}