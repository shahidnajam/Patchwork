<?php
/**
 * Validates a value using a model
 */
class CU_Validate_Model extends Zend_Validate_Abstract {
    const NOT_VALID = 'notValid';

    protected $_messageTemplates = array(
        self::NOT_VALID => '%value% is not valid'
    );

    private $_model = null;
    private $_property = null;

    public function __construct(CU_Model_IModel $model = null, $property = null) {
        if($model !== null) {
            $this->setModel($model);
        }

        if($property !== null) {
            $this->setProperty($property);
        }
    }

    public function setModel(CU_Model_IModel $model) {
        $this->_model = $model;
    }

    public function setProperty($property) {
        $this->_property = (string)$property;
    }

    public function isValid($value) {
        if($this->_model === null || $this->_property === null) {
            throw new RuntimeException('The model or property was not set before attempting to validate');
        }

        $this->_setValue($value);
        $this->_model->set($this->_property, $value);

        if($this->_model->isValid()) {
            return true;
        }

        $errors = $this->_model->getMessages();
        if(array_key_exists($this->_property, $errors)) {
            $this->_error();
            return false;
        }

        return true;
    }
}