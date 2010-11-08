<?php
/**
 * Patchwork
 *
 * @category    Library
 * @package     Patchwork
 * @subpackage  Plugins
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * Firewall Rule
 *
 * created from json doc comment
 * <code>
 * @get {"name": "id", "type": "int", "length": "1-10"}
 * @post {"name": "name", "type": "string", "length": "8-10"}
 * </code>
 *
 * @category    Library
 * @package     Patchwork
 * @subpackage  Plugins
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_Controller_Plugin_Firewall_Rule
implements Zend_Validate_Interface
{
    /**
     * value type, default is "string"
     * @var string
     */
    protected $_type = 'string';
    /**
     * length, range separated by "-"
     * @var int|string
     */
    protected $_length;
    /**
     * validators
     * @var array
     */
    protected $_validators = array();
    /**
     * validation failure messages
     * @var array
     */
    protected $_messages = array();

    /**
     * Constructor
     *
     * @param string $jsonString json string from doc comment
     */
    public function  __construct($jsonString)
    {
        $object = json_decode($jsonString);

        if($object->type == 'bool') {
            $object->type = 'boolean';
        }
        $this->_type = $object->type;

        $this->_length = $object->length;
    }

    /**
     * check if param has valid type and length
     * @param mixed $param param
     * @return boolean
     */
    public function isValid($param)
    {
        if($this->_type == 'int') {
            $param = (int)$param;
        } elseif ($this->type == 'array' && !is_array($param)) {
            $this->_messages[] = 'Is not an array.';
            return false;
        } elseif ($this->type == 'boolean') {
            $param = (bool)$param;
        } elseif ($this->type == 'float') {
            $param = (float)$param;
        }

        if(gettype($param) != $this->_type) {
            $this->_messages[] = 'Invalid type ' . gettype($param);
            return false;
        }

        if(strpos($this->_length, '-') > 0) {
            list($min,$max) = explode('-', $this->_length);
        } else {
            $min = $max = $this->_length;
        }
        if(strlen($param) < $min || strlen($param) >  $max) {
            $this->_messages[] = 'Invalid length: ' . strlen($param);
            return false;
        }
    }

    /**
     * Returns array of validation failure messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

}