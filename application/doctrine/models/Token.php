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
 * Doctrine schema:
 * <code>
  Token:
  tableName: tokens
  actAs: [Timestampable, SoftDelete]
  columns:
  id:
  type: integer(4)
  primary: true
  autoincrement: true
  hash:
  type: string(64)
  notnull: true
  service:
  type: string(255)
  notnull: true
  once:
  type: boolean
  default: 1
  jsoncontext:
  type: string
 * </code>
 *
 * @category   Application
 * @package    Models
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
class Token extends BaseToken implements Patchwork_Token
{
    private $triggeredService;
    private $context;

    /**
     * set the service to be triggered
     *
     * @param Patchwork_Token_Triggered $service service instance
     */
    function setTriggeredService(Patchwork_Token_Triggered $service)
    {
        $this->triggeredService = $service;
    }

    /**
     * get the instance of the service to be triggered
     * @return Patchwork_Token_Triggered
     */
    function getTriggeredService()
    {
        return $this->triggeredService;
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
        if($this->hash == '') {
            $this->_generateHash();
        }
        return $this->hash;
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
    function isMultiplyUsable()
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
        if ($this->hash == null) {
            $this->hash = sha1(uniqid(time(), true));
        }
    }

}