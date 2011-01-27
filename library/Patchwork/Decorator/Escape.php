<?php
/**
 * Patchwork_Decorator_Escape
 *
 * Generic escaping decorator to use in views, primarily for objects. Use
 * instead of using view->escape(...) repeatedly.
 *
 * <code>
 * /** in a controller *\/
 * $this->view = new Patchwork_Decorator_Escape($myObject);
 * </code>
 *
 * @package Patchwork
 * @subpackage Decorator
 * @author Daniel Pozzi
 */
class Patchwork_Decorator_Escape implements ArrayAccess
{
    /**
     * object/data to escape
     * @var mixed
     */
    private $delegatee;

    /**
     * get
     *
     * @param string $name property name
     * @return string
     */
    public function  __get($name)
    {
        return $this->escape($this->delegatee->$name);
    }

    /**
     * escape function calls on delegatee if the result is
     * - an object
     * - an array
     * - integer or string
     * 
     * @param string $name
     * @param string $arguments
     * @return mixed
     */
    public function  __call($name, $arguments)
    {
        $result = call_user_method($name, $this->delegatee, $arguments);
        return $this->decorateResult($result);
    }

    /**
     *
     * @param mixed $result
     * @return self 
     */
    private function decorateResult($result)
    {
        if (is_object($result) || is_array($result)) {
            return new self($result);
        } elseif (is_int($result) || is_string($result)) {
            return $this->escape($result);
        } else {
            return $result;
        }
    }

    /**
     * escape a string
     *
     * @param string $string string to escape
     * @return string escaped string
     */
    private function escape($string)
    {
        return htmlspecialchars($string, 'ENT_QUOTES', 'UTF-8');
    }

    /**
     *
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        if(is_array($this->delegatee)) {
            return isset($this->delegatee[$offset]);
        }

        return false;
    }

    /**
     *
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if(is_array($this->delegatee)) {
            return $this->decorateResult($this->delegatee[$offset]);
        }
    }

    /**
     * @throws RuntimeException
     */
    public function  offsetSet($offset, $value)
    {
        throw new RuntimeException(
            'The escape decorator allows only retrieval of values.'
        );
    }

    /**
     * @throws RuntimeException
     */
    public function  __set($name, $value)
    {
        throw new RuntimeException(
            'The escape decorator allows only retrieval of values.'
        );
    }
}