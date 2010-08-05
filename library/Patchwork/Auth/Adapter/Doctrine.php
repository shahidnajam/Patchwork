<?php
/**
 * Patchwork_Auth_Adapter_Doctrine
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage Authentification
 * @author     Daniel Pozzi
 */

/**
 * Patchwork_Auth_Adapter_Doctrine
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage Authentification
 * @author     Daniel Pozzi
 */
class Patchwork_Auth_Adapter_Doctrine implements Zend_Auth_Adapter_Interface
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
     * @var Doctrine_Record
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
     * attempt an authenication.
     *
     * @throws Zend_Auth_Adapter_Exception
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        $this->_checkOptions();
        
        $dql = $this->_identityColumn . ' = ? AND ' . $this->_credentialColumn
            . ' = ' . $this->_credentialTreatment;
        try {
            $resultIdentities = Doctrine::getTable($this->_tableName)->findByDql(
                $dql,
                array( $this->_identity, $this->_credential )
            );
        } catch (Exception $e) {
            throw new Zend_Auth_Adapter_Exception($e->getMessage());
        }

        if ($this->_isValidResultSet($resultIdentities)) {
            $this->_authenticateResultInfo['code'] = Zend_Auth_Result::SUCCESS;
            $this->_authenticateResultInfo['messages'][] =
                'Authentication successful.';
            $this->_result = $resultIdentities->getFirst();
        }

        return $this->_getAuthResult();
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
     * validate the result collection
     *
     * @param Doctrine_Collection $collection
     * @return boolean
     */
    protected function _isValidResultSet(Doctrine_Collection $collection)
    {
        if ($collection->count() < 1) {
            $result = Doctrine::getTable($this->_tableName)->findByDql(
                $this->_identityColumn . ' = ?', array( $this->_identity )
            );
            
            if (count($result) > 0) {
                // only wrong password
                $this->_authenticateResultInfo['code'] =
                        Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
                $this->_authenticateResultInfo['messages'][] =
                        'The supplied credential is invalid.';
            } else {
                // incorrect identity and password
                $this->_authenticateResultInfo['code'] =
                        Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
                $this->_authenticateResultInfo['messages'][] = 
                    'A record with the supplied identity could not be found.';
            }
            return false;
        } elseif ($collection->count() > 1) {
            $this->_authenticateResultInfo['code'] =
                    Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS;
            $this->_authenticateResultInfo['messages'][] = 
                'More than entry matches the supplied identity.';
            return false;
        }
        
        return true;
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