<?php
/**
 * Patchwork_Auth_Adapter_DB
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage Authentification
 * @author     Daniel Pozzi
 */

/**
 * Patchwork_Auth_Adapter_DB
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage Authentification
 * @author     Daniel Pozzi
 */
abstract class Patchwork_Auth_Adapter_DB
{

    /**
     * $_tableName - the table name to check
     * @var string
     */
    protected $_tableName = null;

    /**
     * $_identityColumn - the column to use as the identity
     * @var string
     */
    protected $_identityColumn = null;

    /**
     * $_credentialColumns - columns to be used as the credentials
     * @var string
     */
    protected $_credentialColumn = null;

    /**
     * $_identity - Identity value
     * @var string
     */
    protected $_identity = null;

    /**
     * $_credential - Credential values
     * @var string
     */
    protected $_credential = null;

    /**
     * $_credentialTreatment - Treatment applied to the credential,
     such as MD5() or PASSWORD()
     * @var string
     */
    protected $_credentialTreatment = null;

    /**
     * $_authenticateResultInfo
     * @var array
     */
    protected $_authenticateResultInfo = null;

    /**
     * identity
     * @var object
     */
    protected $_result = null;

    /**
     * constructor requires configuration options
     *
     * @param string $tableName
     * @param string $identityColumn
     * @param string $credentialColumn
     * @param string $credentialTreatment
     */
    public function __construct(
            $tableName,
            $identityColumn,
            $credentialColumn,
            $credentialTreatment
    ) {
        $this->_tableName = $tableName;
        $this->_identityColumn = $identityColumn;
        $this->_credentialColumn = $credentialColumn;
        $this->_credentialTreatment = $credentialTreatment;
    }

    /**
     * assures that valid options have been passed to the constructor
     *
     * @throws Zend_Auth_Adapter_Exception
     * @return boolean
     */
    protected function _checkOptions()
    {
        $exception = null;
        if ($this->_tableName == '') {
            $exception = 'A table must be supplied for the authentication adapter.';
        } elseif ($this->_identityColumn == '') {
            $exception = 'An identity column must be supplied for the authentication adapter.';
        } elseif ($this->_credentialColumn == '') {
            $exception = 'A credential column must be supplied for the authentication adapter.';
        }

        if ($exception !== null) {
            throw new Zend_Auth_Adapter_Exception($exception);
        }

        $this->_authenticateResultInfo = array(
                'code'     => Zend_Auth_Result::FAILURE,
                'identity' => $this->_identity,
                'messages' => array()
        );

        return true;
    }

    /**
     * set the identity
     *
     * @param string $identity
     * @return Patchwork_Auth_Adapter_Doctrine
     */
    public function setIdentity($identity)
    {
        if ($identity == '')
            throw new Zend_Auth_Adapter_Exception(
                'A value for the identity was not provided for authentication.'
            );
        $this->_identity = $identity;
        return $this;
    }

    /**
     * set the credential
     *
     * @param string $credential
     * @return Patchwork_Auth_Adapter_Doctrine
     */
    public function setCredential($credential)
    {
        if ($credential === null) {
            throw new Zend_Auth_Adapter_Exception(
                'A credential value was not provided for authentication.'
            );
        }
        $this->_credential = $credential;
        return $this;
    }

    /**
     * Return the identity
     *
     * @return Doctrine_Record
     */
    public function getAuthIdentity()
    {
        return $this->_result;
    }

    /**
     *  creates a Zend_Auth_Result object from the information that has been
     * collected during the authenticate() attempt.
     *
     * @return Zend_Auth_Result
     */
    protected function _getAuthResult()
    {
        return new Zend_Auth_Result(
                $this->_authenticateResultInfo['code'],
                $this->_authenticateResultInfo['identity'],
                $this->_authenticateResultInfo['messages']
        );
    }
}
