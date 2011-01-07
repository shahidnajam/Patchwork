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
class Patchwork_Auth_Adapter_Doctrine
    extends Patchwork_Auth_Adapter_DB
    implements Patchwork_Auth_DBAdapter
{
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
            $resultIdentities = Doctrine::getTable($this->_tableName)
                ->findByDql(
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
     *
     * @return array
     */
    public function getAuthIdentityData()
    {
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $record = Doctrine::getTable(get_class($this->getAuthModel()))
                ->findOneBy($this->_identityColumn, $this->_identity);

            return $record->toArray();
        }
    }
}