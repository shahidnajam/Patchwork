<?php
/**
 * Patchwork_Auth_Model
 * 
 * allows authentication
 *
 * @category   Library
 * @package    Authentication
 * @subpackage AuthModel
 * @author     Daniel Pozzi
 */
interface Patchwork_Auth_Model
{
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