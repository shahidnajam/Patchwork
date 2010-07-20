<?php
/**
 * Patchwork_Auth_Adapter_Doctrine
 *
 * @package Patchwork
 * @subpackage Authentification
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
     * $_resultRow - Results of database authentication query
     * @var array
     */
    protected $_resultRow = null;

    /**
     * __construct() - Sets configuration options
     *
     * @param string $tableName
     * @param string $identityColumn
     * @param string $credentialColumn
     * @param string $credentialTreatment
     * @return void
     */
    public function __construct(
            $tableName = null,
            $identityColumn = null,
            $credentialColumn = null,
            $credentialTreatment = null
    ) {
        if (null !== $tableName) {
            $this->setTableName($tableName);
        }

        if (null !== $identityColumn) {
            $this->setIdentityColumn($identityColumn);
        }

        if (null !== $credentialColumn) {
            $this->setCredentialColumn($credentialColumn);
        }

        if (null !== $credentialTreatment) {
            $this->setCredentialTreatment($credentialTreatment);
        }
    }

    /**
     * setTableName() - set the table name to be used in the select query
     *
     * @param  string $tableName
     * @return Zend_Auth_Adapter_DbTable Provides a fluent interface
     */
    public function setTableName($tableName) {
        $this->_tableName = $tableName;
        return $this;
    }

    /**
     * setIdentityColumn() - set the column name to be used as the
     identity column
     *
     * @param  string $identityColumn
     * @return Zend_Auth_Adapter_DbTable Provides a fluent interface
     */
    public function setIdentityColumn($identityColumn) {
        $this->_identityColumn = $identityColumn;
        return $this;
    }

    /**
     * setCredentialColumn() - set the column name to be used as the
     credential column
     *
     * @param  string $credentialColumn
     * @return Zend_Auth_Adapter_DbTable Provides a fluent interface
     */
    public function setCredentialColumn($credentialColumn) {
        $this->_credentialColumn = $credentialColumn;
        return $this;
    }

    /**
     * setCredentialTreatment() - allows the developer to pass a
     parameterized string that is
     * used to transform or treat the input credential data
     *
     * In many cases, passwords and other sensitive data are
     encrypted, hashed, encoded,
     * obscured, or otherwise treated through some function or
     algorithm. By specifying a
     * parameterized treatment string with this method, a developer
     may apply arbitrary SQL
     * upon input credential data.
     *
     * Examples:
     *
     *  'PASSWORD(?)'
     *  'MD5(?)'
     *
     * @param  string $treatment
     * @return Zend_Auth_Adapter_DbTable Provides a fluent interface
     */
    public function setCredentialTreatment($treatment) {
        $this->_credentialTreatment = $treatment;
        return $this;
    }

    /**
     * setIdentity() - set the value to be used as the identity
     *
     * @param  string $value
     * @return Zend_Auth_Adapter_DbTable Provides a fluent interface
     */
    public function setIdentity($value) {
        $this->_identity = $value;
        return $this;
    }

    /**
     * setCredential() - set the credential value to be used,
     optionally can specify a treatment
     * to be used, should be supplied in parameterized form, such as
     'MD5(?)' or 'PASSWORD(?)'
     *
     * @param  string $credential
     * @return Zend_Auth_Adapter_DbTable Provides a fluent interface
     */
    public function setCredential($credential) {
        $this->_credential = $credential;
        return $this;
    }


    /**
     * authenticate() - defined by Zend_Auth_Adapter_Interface.  This
     * method is called to attempt an authenication.  Previous to this call,
     * this adapter would have already been configured with all nessissary
     * information to successfully connect to a database table and attempt to
     * find a record matching the provided identity.
     *
     * @throws Zend_Auth_Adapter_Exception if answering the
     authentication query is impossible
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        $this->_authenticateSetup();
        $table = $this->getDoctrineTable();
        try {
            $dql = $this->_identityColumn . ' = ? AND ' . $this->_credentialColumn
                    . ' = ' . $this->_credentialTreatment;
            
            $resultIdentities = $table->findByDql(
                $dql,
                array( $this->_identity, $this->_credential )
            );
        } catch (Exception $e) {
            throw new Zend_Auth_Adapter_Exception($e->getMessage());
        }
        if ( ($authResult =
        $this->_authenticateValidateResultset($resultIdentities)) instanceof
        Zend_Auth_Result) {
            return $authResult;
        }
        $this->_resultRow = $resultIdentities->getFirst()->toArray();
        $authResult =
                $this->_authenticateValidateResult($resultIdentities->getFirst());
        return $authResult;
    }

    /**
     * Return an array of the result row
     *
     * @return array
     */
    public function getResultRow() {
        return $this->_resultRow;
    }
    /**
     * get Doctrine_Table for authenticate
     *
     * @return Doctrine_Table
     */

    public function getDoctrineTable() {
        return Doctrine::getTable($this->_tableName);
    }

    /**
     * _authenticateSetup() - This method abstracts the steps involved
     * with making sure
     * that this adapter was indeed setup properly with all required
     * peices of information.
     *
     * @throws Zend_Auth_Adapter_Exception - in the event that setup
     * was not done properly
     * @return true
     */
    protected function _authenticateSetup() {
        $exception = null;

        if ($this->_tableName == '') {
            $exception = 'A table must be supplied for the
Zend_Auth_Adapter_DbTable authentication adapter.';
        } elseif ($this->_identityColumn == '') {
            $exception = 'An identity column must be supplied for the
Zend_Auth_Adapter_DbTable authentication adapter.';
        } elseif ($this->_credentialColumn == '') {
            $exception = 'A credential column must be supplied for the
Zend_Auth_Adapter_DbTable authentication adapter.';
        } elseif ($this->_identity == '') {
            $exception = 'A value for the identity was not provided
prior to authentication with Zend_Auth_Adapter_DbTable.';
        } elseif ($this->_credential === null) {
            $exception = 'A credential value was not provided prior to
authentication with Zend_Auth_Adapter_DbTable.';
        }

        if (null !== $exception) {
            /**
             * @see Zend_Auth_Adapter_Exception
             */
            require_once 'Zend/Auth/Adapter/Exception.php';
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
     * _authenticateValidateResultSet() - This method attempts to make
     * certian that only one record was returned in the result set
     *
     * @param array $resultIdentities
     * @return true|Zend_Auth_Result
     */
    protected function _authenticateValidateResultSet(
            Doctrine_Collection $resultIdentities
    ) {
        if ($resultIdentities->count() < 1) {
            // check if the "username" was correct
            $result = $this->getDoctrineTable()->findByDql(
                    $this->_identityColumn . ' = ?', array( $this->_identity ) );
            if (count($result) > 0) {
                // only wrong password
                $this->_authenticateResultInfo['code'] =
                        Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID;
                $this->_authenticateResultInfo['messages'][] =
                        'Supplied credential is invalid.';
            } else {
                // incorrect identity and password
                $this->_authenticateResultInfo['code'] =
                        Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
                $this->_authenticateResultInfo['messages'][] = 'A
record with the supplied identity could not be found.';
            }
            return $this->_authenticateCreateAuthResult();
        } elseif ($resultIdentities->count() > 1) {
            $this->_authenticateResultInfo['code'] =
                    Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS;
            $this->_authenticateResultInfo['messages'][] = 'More than
one record matches the supplied identity.';
            return $this->_authenticateCreateAuthResult();
        }
        return true;
    }

    /**
     * _authenticateValidateResult() - This method attempts to
     validate that the record in the
     * result set is indeed a record that matched the identity
     provided to this adapter.
     *
     * @param array $resultIdentity
     * @return Zend_Auth_Result
     */
    protected function _authenticateValidateResult(
            Doctrine_Record $resultIdentity
    ) {
        $this->_authenticateResultInfo['code'] = Zend_Auth_Result::SUCCESS;
        $this->_authenticateResultInfo['messages'][] = 'Authentication
successful.';
        return $this->_authenticateCreateAuthResult();
    }

    /**
     * _authenticateCreateAuthResult() - This method creates a
     * Zend_Auth_Result object
     * from the information that has been collected during the
     * authenticate() attempt.
     *
     * @return Zend_Auth_Result
     */
    protected function _authenticateCreateAuthResult() {
        return new Zend_Auth_Result(
                $this->_authenticateResultInfo['code'],
                $this->_authenticateResultInfo['identity'],
                $this->_authenticateResultInfo['messages']
        );
    }
}