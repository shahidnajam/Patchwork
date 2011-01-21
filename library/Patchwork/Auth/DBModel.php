<?php
/**
 * Patchwork_Auth_DBModel
 * 
 * allows authentication against a database table
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage Auth
 * @author     Daniel Pozzi
 */
interface Patchwork_Auth_DBModel
{
    public function getAuthenticationTable();
    public function getAuthenticationIdentityColumn();
    public function getAuthenticationCredentialColumn();
    public function getAuthenticationCredentialTreatment();
    
    public function getAuthenticationIdentity();
    public function getAuthenticationCredential();

    /**
     * salt the password
     */
    public function getSaltedPassword($password);

    /**
     * the stored pw
     */
    public function getSharedSecret();
}