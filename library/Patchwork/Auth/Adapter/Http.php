<?php
/**
 * Patchwork_Auth_Adapter_Http
 *
 * Same as Zend's auth adapter, but uses Patchwork's resolver to salt and md5
 * the password
 *
 * @category Library
 * @package  Authentication
 * 
 */
class Patchwork_Auth_Adapter_Http extends Zend_Auth_Adapter_Http
{
    /**
     * Basic Authentication
     *
     * @param  string $header Client's Authorization header
     * @throws Zend_Auth_Adapter_Exception
     * @return Zend_Auth_Result
     */
    protected function _basicAuth($header)
    {
        if (empty($header)) {
            /**
             * @see Zend_Auth_Adapter_Exception
             */
            throw new Zend_Auth_Adapter_Exception(
                'The value of the client Authorization header is required'
            );
        }
        if (empty($this->_basicResolver)) {
            /**
             * @see Zend_Auth_Adapter_Exception
             */
            throw new Zend_Auth_Adapter_Exception(
                'A basicResolver object must be set before doing Basic '
                    . 'authentication');
        }

        // Decode the Authorization header
        $auth = substr($header, strlen('Basic '));
        $auth = base64_decode($auth);
        if (!$auth) {
            /**
             * @see Zend_Auth_Adapter_Exception
             */
            throw new Zend_Auth_Adapter_Exception('Unable to base64_decode Authorization header value');
        }

        // See ZF-1253. Validate the credentials the same way the digest
        // implementation does. If invalid credentials are detected,
        // re-challenge the client.
        if (!ctype_print($auth)) {
            return $this->_challengeClient();
        }
        // Fix for ZF-1515: Now re-challenges on empty username or password
        $creds = array_filter(explode(':', $auth));
        if (count($creds) != 2) {
            return $this->_challengeClient();
        }

        $password = $this->_basicResolver->resolve($creds[0], $this->_realm);
        $salted = $this->_basicResolver->getAuthModel()->getSaltedPassword($creds[1]);
        if ($password && $password === $salted) {
            $identity = array('username'=>$creds[0], 'realm'=>$this->_realm);
            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $identity);
        } else {
            return $this->_challengeClient();
        }
    }
}