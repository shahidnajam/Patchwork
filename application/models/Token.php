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
class Token extends BaseToken
{

    /**
     * factory method
     * 
     * @param string  $triggers triggered action
     * @param mixed   $context  context data
     * @param boolean $multiple multiple usage flag (default is false)
     *
     * @return Token
     */
    public static function factory($triggers, $context = null, $multiple = false)
    {
        $token = new Token;
        $token->service = (string) $triggers;
        $token->jsoncontext = serialize($context);
        $token->once = !$multiple;
        $token->_generateHash();
        $token->save();
        return $token;
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

    /**
     * returns the json-decoded context data
     * 
     * @return mixed
     */
    public function getContext()
    {
        return unserialize($this->jsoncontext);
    }

    /**
     * trigger the service
     *
     * @return TokenTriggered
     */
    public function trigger()
    {
        $className = $this->service;
        $service = new $className;
        if (!$service instanceof TokenTriggered)
            throw new RuntimeException(
                $className . ' does not implement TokenTriggered',
                404
            );

        if ($this->once) {
            $this->delete();
            $this->deleted_at = date('Y-m-d H:i');
        }

        return $service->startWithToken($this);
    }

}