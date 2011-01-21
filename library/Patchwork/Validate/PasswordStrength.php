<?php
/**
 * Password Strength Validator
 * 
 * @link      http://framework.zend.com/manual/en/zend.validate.writing_validators.html
 * @copyright Zend.com
 */
class Patchwork_Validate_PasswordStrength extends Zend_Validate_Abstract
{
    const LENGTH = 'length';
    const UPPER  = 'upper';
    const LOWER  = 'lower';
    const DIGIT  = 'digit';
    const SPECIAL= 'special';

    /**
     * min length
     * @var int
     */
    private $minimumLength = 8;
    /**
     *
     * @var boolean
     */
    private $requiresSpecialChar = false;

    /**
     * error messages
     * @var array
     */
    protected $_messageTemplates = array(
        self::LENGTH => "'%value%' must be at least 8 characters in length",
        self::UPPER  => "'%value%' must contain at least one uppercase letter",
        self::LOWER  => "'%value%' must contain at least one lowercase letter",
        self::DIGIT  => "'%value%' must contain at least one digit character",
        self::SPECIAL  => "'%value%' must contain at least one special character (non-letter, non-number)",
    );

    /**
     * set the minimum length of the pw
     * 
     * @param int $length
     * @return Patchwork_Validate_PasswordStrength
     */
    public function setMinimumLength($length)
    {
        $this->minimumLength = $length;
        return $this;
    }

    /**
     *
     * @param bool $flag
     * @return Patchwork_Validate_PasswordStrength
     */
    public function setRequiresSpecialChar($flag)
    {
        $this->requiresSpecialChar = (bool)$flag;
        return $this;
    }
    
    /**
     * check if string matches criteria
     * 
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue($value);

        $isValid = true;

        if (strlen($value) < $this->minimumLength) {
            $this->_error(self::LENGTH);
            $isValid = false;
        }

        if (!preg_match('/[A-Z]/', $value)) {
            $this->_error(self::UPPER);
            $isValid = false;
        }

        if (!preg_match('/[a-z]/', $value)) {
            $this->_error(self::LOWER);
            $isValid = false;
        }

        if (!preg_match('/\d/', $value)) {
            $this->_error(self::DIGIT);
            $isValid = false;
        }

        if($this->requiresSpecialChar) {
            if (!preg_match('/[^a-zA-Z0-9]/', $value)) {
                $this->_error(self::SPECIAL);
                $isValid = false;
            }
        }

        return $isValid;
    }
}