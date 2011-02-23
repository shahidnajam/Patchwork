<?php

/**
 * Patchwork
 *
 * @category Application
 * @package  Models
 * @author   Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * Token
 *
 * Token with a hash to control execution of an action, which is implemented as
 * call to a service class implementing the TokenTriggered interface.
 * Tokens can be used once or multiple, one-time usage tokens delete themself.
 *
 * A token is created using the factory method factory,
 *
 *
 * @category   Application
 * @package    Models
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
class Core_Model_Token extends Core_Model_Base_Token implements Patchwork_Token
{
    private $context;

    /**
     * set the service to be triggered
     *
     * @param Patchwork_Token_Triggered $service service instance
     */
    function setTriggeredService(Patchwork_Token_Triggered $service)
    {
        $this->service = get_class($service);
    }

    /**
     * get the instance of the service to be triggered
     * @return string
     */
    function getTriggeredServiceName()
    {
        return $this->service;
    }

    /**
     * set contextual data
     * @param array $context
     */
    function setContext(array $context)
    {
        $this->context = serialize($context);
        return $this;
    }

    /**
     * get contextual data
     *
     */
    function getContext()
    {
        return unserialize($this->context);
    }

    /**
     * get the hash
     * @return string
     */
    function getHash()
    {
        if($this->_get('hash') == '') {
            $this->_generateHash();
        }
        return $this->_get('hash');
    }

    /**
     * set mutliple usage
     * @param boolean $flag flag
     */
    function setMultipleUse($flag)
    {
        $this->once = !$flag;
        return $this;
    }

    /**
     * check if token can be use more than once
     * @return boolean
     */
    function isMultipleUsable()
    {
        return !$this->once;
    }

    /**
     * generates and sets the hash if own hash is null
     *
     * @return void
     */
    protected function _generateHash()
    {
        if ($this->_get('hash') == null) {
            $this->hash = sha1(uniqid(time(), true));
        }
    }

    public function  save(Doctrine_Connection $conn = null)
    {
        $this->_generateHash();
        parent::save($conn);
    }
}
