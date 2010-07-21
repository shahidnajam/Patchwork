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
    implements Patchwork_Form_Doctrine_Renderable, Patchwork_Auth_RoleIdentity
{
    const AUTH_IDENTITY_COLUMN = 'email';
    const AUTH_CREDENTIAL_COLUMN = 'password';
    const AUTH_SALT_COLUMN = 'salt';
    const AUTH_CREDENTIAL_TREATMENT = 'MD5(CONCAT(?, salt))';

    /**
     * fields not to show in modelform
     *
     * @return array
     */
    public function getIgnoredColumns() {
        return array('created_at', 'updated_at', 'deleted_at', 'password', 'salt');
    }

    /**
     * return the roel
     * 
     * @return string
     */
    public function getRole()
    {
        var_dump($this->toArray());
        return $this->role;
    }
}