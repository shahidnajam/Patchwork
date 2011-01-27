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
abstract class Patchwork_Auth_Adapter_DB implements Patchwork_Auth_DBAdapter
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
     *
     * @var Patchwork_Storage_Service
     */
    private $storageService;

    /**
     *
     * @var Patchwork_Auth_Model
     */
    private $authModel;

    /**
     * constructor
     * 
     * @param Patchwork_Storage_Service $service storage service
     * @param Patchwork_Auth_DBModel    $model   model instance
     */
    public function __construct(
        Patchwork_Storage_Service $service,
        Patchwork_Auth_DBModel $model

    ) {
        $this->storageService = $service;
        $this->authModel = $model;

        $this->_tableName = $model->getAuthenticationTable();
        $this->_identityColumn = $model->getAuthenticationIdentityColumn();
        $this->_credentialColumn = $model->getAuthenticationCredentialColumn();
        $this->setCredentialTreatment(
            $model->getAuthenticationCredentialTreatment()
         );
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
     * @return Patchwork_Auth_Adapter_DB
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
     * set credential treatment
     * 
     * @param string $treatment
     * @return Patchwork_Auth_Adapter_DB
     */
    public function setCredentialTreatment($treatment)
    {
        $this->_credentialTreatment = $treatment;
        return $this;
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

    /**
     * get the injected storage service
     * 
     * @return Patchwork_Storage_Service
     */
    protected function getStorageService()
    {
        return $this->storageService;
    }

    /**
     * get injected model
     * 
     * @return Patchwork_Auth_DBModel
     */
    protected function getAuthModel()
    {
        return $this->authModel;
    }
}
