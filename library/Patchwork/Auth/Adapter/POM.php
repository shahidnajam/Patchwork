<?php
/**
 * Authentication Adapter for POM
 *
 * 
 */
class Patchwork_Auth_Adapter_POM 
    extends Patchwork_Auth_Adapter_DB
    implements Patchwork_Auth_DBAdapter
{
    /**
     * authenticate
     *
     * 
     */
    public function authenticate()
    {
        $this->_checkOptions();

        $sql = 'SELECT * FROM ' . $this->_tableName . ' WHERE ('
                . $this->_identityColumn . ' = ? AND ' . $this->_credentialColumn
                . ' = ' . $this->_credentialTreatment . ')';
        $params = array( $this->_identity, $this->_credential );

        try {
            $resultIdentities = $this->getStorageService()->query(
                $sql,
                $params,
                get_class($this->getAuthModel())
            );
        } catch (Exception $e) {
            throw new Zend_Auth_Adapter_Exception($e->getMessage());
        }

        if ($this->_isValidResultSet($resultIdentities)) {
            $this->_authenticateResultInfo['code'] = Zend_Auth_Result::SUCCESS;
            $this->_authenticateResultInfo['messages'][] =
                'Authentication successful.';
            $this->_result = $resultIdentities[0];
        }

        return $this->_getAuthResult();
    }

    /**
     * validate the result collection
     *
     * @param array $resultSet
     * @return boolean
     */
    private function _isValidResultSet(array $resultSet)
    {
        if (count($resultSet) !== 1) {
            $this->_authenticateResultInfo['code'] =
                    Zend_Auth_Result::FAILURE;
            $this->_authenticateResultInfo['messages'][] =
                    'The identity does not exist or the password is wrong.';
            return false;
        }

        return true;
    }
}