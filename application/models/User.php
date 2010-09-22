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
    Patchwork_Auth_Model
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
     * authenticate using Zend_Auth
     *
     * @param string $identity   identity
     * @param string $credential credential (password)
     *
     * @return boolean
     */
    public static function authenticate($identity, $credential)
    {
        $adapter = new Patchwork_Auth_Adapter_Doctrine(
            __CLASS__,
            self::AUTH_IDENTITY_COLUMN,
            self::AUTH_CREDENTIAL_COLUMN,
            self::AUTH_CREDENTIAL_TREATMENT
            );
		$adapter->setIdentity($identity)->setCredential($credential);
		self::$_authResult = Zend_Auth::getInstance()->authenticate($adapter);

		/*
		 * save object on success
		 */
		if(self::$_authResult->isValid() && Zend_Auth::getInstance()->hasIdentity())
		{
			Zend_Auth::getInstance()->getStorage()->write(
                $adapter->getAuthIdentity()->toArray()
            );
			return true;
		}
        return false;
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
}