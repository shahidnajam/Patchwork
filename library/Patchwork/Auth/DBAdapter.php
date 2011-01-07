<?php
/**
 * Interface for adapter to authenticate against databases
 *
 * 
 */
interface Patchwork_Auth_DBAdapter extends Zend_Auth_Adapter_Interface
{
    function setIdentity($identity);
    function setCredential($credential);

    /**
     * get the data of Zend_Auth identity as array
     * @return array
     */
    function getAuthIdentityData();
}