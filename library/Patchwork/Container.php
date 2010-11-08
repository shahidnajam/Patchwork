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
 * on its own, inspired by Horde's Injector
 *
 * @category    Library
 * @package     Patchwork
 * @subpackage  Container
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_Container
{
    const CONFIG_BINDING_IMPL = 'bindImplementation';

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
     * Constructor
     *
     * can take options from application.ini, automatically sets itself,
     * automatically binds Zend_Controller_Front as its own factory
     *
     * @param array $options options for bindings
     */
    public function  __construct(array $options = NULL)
    {
        if(is_array($options) && isset($options['patchwork']['container'])){
            $this->_setBindingsFromConfig($options['patchwork']['container']);
        }

        $this->set('Patchwork_Container', $this);
        $this->bindFactory(
            'Zend_Controller_Front',
            'Zend_Controller_Front',
            'getInstance'
        );
    }

    /**
     * check if a binding etc is registered
     *
     * @param string $name identifier, interface or class name
     *
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->$name)
            || array_key_exists($name, $this->_bindings)
            || array_key_exists($name, $this->_factories);
    }

    /**
     * set config
     * 
     * @param array $config from application.ini
     * @return void
     */
    private function _setBindingsFromConfig(array $config)
    {
        foreach($config as $name => $bindingType){
            if(isset($bindingType[self::CONFIG_BINDING_IMPL])){
                $this->bindImplementation(
                    $name,
                    $bindingType[self::CONFIG_BINDING_IMPL]
                );
            } else {
                $this->bindFactory(
                    $name,
                    $bindingType['bindFactory'],
                    $bindingType['bindFactoryMethod']
                );
            }
        }
    }

    

    /**
     * retrieve an instance
     *
     * @param string $name                   class or interface name
     *
     * @return object
     */
    public function __get($name)
    {
        return $this->getInstance($name, false);
    }

    /**
     * same as get, but allows direct isntantiation
     * 
     *
     * @param string $name                   class or interface name
     * @param bool   $tryDirectInstantiation try "new" $name
     */
    public function getInstance($name, $tryDirectInstantiation = true)
    {
        if(isset($this->$name)){
            return $this->$name;
        }

        $resource = strtolower($name);
        if (isset($this->$resource)) {
            return $this->$resource;
        }

        return $this->createInstance($name, $tryDirectInstantiation);
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
        if($name != get_class($class)) {
            $this->__set(get_class($class), $class);
        }
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
     * @param string $name                   class or interface name
     * @param string $tryDirectInstantiation try directInstantiation
     *
     * @return object $name
     */
    public function createInstance($name, $tryDirectInstantiation = false)
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

        if($tryDirectInstantiation == false) {
            throw new Patchwork_Exception('Not able to resolve "' . $name . '"');
        } else {
            return $this->_createInstance($name);
        }
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
        $instance = $this->getInstance($className);
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
        if(!class_exists($name)) {
            throw new Patchwork_Exception('Could not resolve class ' . $name);
        }
        $ref = new ReflectionClass($name);
        if($ref->isAbstract()) {
            throw new Patchwork_Exception($name . ' is an abstract class');
        }
        $constructor = $ref->getConstructor();
        if(!$constructor) {
            return new $name;
        }
        
        $params = $ref->getConstructor()->getParameters();
        $args = array();
        foreach ($params as $param) {
            $reqClass = $param->getClass()->getShortName();
            if (isset($this->$reqClass) || class_exists($reqClass) || interface_exists($reqClass)) {
                $args[] = $this->__get($reqClass);
            } else {
                throw new Patchwork_Exception(
                    'Could not resolve dependency ' . $reqClass .' for '. $name
                );
            }
        }

        $ref = new ReflectionClass($name);
        return $ref->newInstanceArgs($args);
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

    /**
     * lists all bindings
     * 
     * @return array
     */
    public function getBindings()
    {
        return $this->_bindings;
    }
}