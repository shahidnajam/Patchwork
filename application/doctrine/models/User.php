<?php
/**
 * User
 *
 * @package    Application
 * @subpackage Models
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * User class
 * 
 * @package    Application
 * @subpackage Models
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
class User
    extends BaseUser
    implements Zend_Acl_Role_Interface,
    Patchwork_Doctrine_Model_Renderable,
    Patchwork_Auth_DBModel
{
    const AUTH_IDENTITY_COLUMN = 'email';
    const AUTH_CREDENTIAL_COLUMN = 'password';
    const AUTH_SALT_COLUMN = 'salt';
    const AUTH_CREDENTIAL_TREATMENT = 'MD5(CONCAT(?, salt))';

    /**
     * instance of User
     * @var User
     */
    protected static $authenticatedUser;

    /**
     *
     * @var Zend_Auth_Adapter_Interface
     */
    protected static $authAdapter;


    /**
     * result of auth
     * @var Zend_Auth_Result
     */
    protected static $_authResult;

    /**
     * fields not to show in modelform
     *
     * @return array
     */
    public function getIgnoredColumns()
    {
        return array(
            'deleted_at',
            'password',
            'salt',
        );
    }

    /**
     * return the role for ACL (Zend_Acl_Role_Interface)
     * 
     * @return string
     */
    public function getRoleId()
    {
        return $this->_get('role');
    }

    /**
     * returns the authentication result
     * 
     * @return Zend_Auth_Result
     */
    public static function getAuthenticationResult()
    {
        return self::$_authResult;
    }

    /**
     * for Patchwork_Auth_Model interface, return the authenticated user
     *
     * @return User|null
     */
    public static function getAuthenticatedUser()
    {
        if(Zend_Auth::getInstance()->hasIdentity()){
            if(self::$authenticatedUser == null){
                $data = Zend_Auth::getInstance()->getIdentity();
                self::$authenticatedUser = Doctrine::getTable(__CLASS__)
                    ->findOneBy(
                    self::AUTH_IDENTITY_COLUMN,
                    $data[self::AUTH_IDENTITY_COLUMN]
                );
            }
            
            return self::$authenticatedUser;
        }
    }

    public function getAuthenticationTable()
    {
        return __CLASS__;
    }
    
    public function getAuthenticationIdentityColumn()
    {
        return self::AUTH_IDENTITY_COLUMN;
    }
    
    public function getAuthenticationCredentialColumn()
    {
        return self::AUTH_CREDENTIAL_COLUMN;
    }

    public function getAuthenticationCredentialTreatment()
    {
        return self::AUTH_CREDENTIAL_TREATMENT;
    }

    public function getAuthenticationIdentity()
    {
        return $this->username;
    }

    public function getAuthenticationCredential()
    {
        return $this->password;
    }
    
    public function getSharedSecret()
    {
        return $this->password;
    }
    
    public function getSaltedPassword($password)
    {
        return md5($password.$this->salt);
    }
}