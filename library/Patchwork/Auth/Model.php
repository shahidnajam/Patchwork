<?php
/**
 * Patchwork_Auth_Model
 * 
 * allows authentication
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage Auth
 * @author     Daniel Pozzi
 */
interface Patchwork_Auth_Model
{
    /**
     * set an authentication adapter
     *
     * @param Zend_Auth_Adapter_Interface $adapter
     */
    public static function setAdapter(Zend_Auth_Adapter_Interface $adapter);

    /**
     * authenticate against the db using the Patchwork Doctrine Adapter
     *
     * @param string $identity
     * @param string $credential
     *
     * @return 
     */
    public static function authenticate($identity, $credential);

    /**
     * returns the auth result
     *
     * @return Zend_Auth_Result
     */
    public static function getAuthenticationResult();

    /**
     * returns instance of self using Zend_Auth stored data
     *
     * @return self
     */
    public static function getAuthenticatedUser();
}