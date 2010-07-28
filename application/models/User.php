<?php
/**
 * User
 *
 * @package    Application
 * @subpackage Models
 * @author     Daniel Pozzi
 */

/**
 * User class
 * 
 * @package    Application
 * @subpackage Models
 * @author     Daniel Pozzi
 */
class User
    extends BaseUser
    implements
    Patchwork_Doctrine_Model_Renderable,
    Patchwork_Auth_RoleIdentity,
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
     * return the roel
     * 
     * @return string
     */
    public function getRole()
    {
        //return $this->role;
    }

    /**
     *
     * @param <type> $identity
     * @param <type> $credential
     *
     * @return boolean
     */
    public static function authenticate($identity, $credential)
    {
        $adapter = new Patchwork_Auth_Adapter_Doctrine(
            'User',
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
     * @return self
     */
    public static function getAuthenticatedUser()
    {
        if(Zend_Auth::getInstance()->hasIdentity()){
            if(self::$authenticatedUser == null){
                $data = Zend_Auth::getInstance()->getIdentity();
                self::$authenticatedUser = Doctrine::getTable('User')->findOneBy(
                    self::AUTH_IDENTITY_COLUMN,
                    $data[self::AUTH_IDENTITY_COLUMN]
                );
            }
            
            return self::$authenticatedUser;
        }
    }
}