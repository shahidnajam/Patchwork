<?php
/**
 * Interface for adapter to authenticate against databases
 *
 * @package    Patchwork
 * @subpackage Authentication
 * @author     Daniel Pozzi
 */
interface Patchwork_Auth_DBAdapter extends Zend_Auth_Adapter_Interface
{
    /**
     * set the identity
     *
     * @param string $identity username etc
     */
    function setIdentity($identity);

    /**
     * set the credential for authentication
     *
     * @param string $credential password etc
     */
    function setCredential($credential);

    /**
     * get the data of Zend_Auth identity
     * 
     * @return Patchwork_Auth_DBModel
     */
    function getAuthModel();
}