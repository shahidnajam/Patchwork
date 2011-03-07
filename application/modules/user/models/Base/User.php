<?php

/**
 * User_Model_Base_User
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property enum $gender
 * @property string $username
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $language
 * @property string $password
 * @property string $salt
 * @property enum $status
 * @property integer $role_id
 * @property User_Model_Role $Role
 * @property Doctrine_Collection $Core_Model_Preference
 * @property Doctrine_Collection $User_Model_Role
 * @property Doctrine_Collection $User_Model_Preference
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class User_Model_Base_User extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('users');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('gender', 'enum', null, array(
             'type' => 'enum',
             'values' => 
             array(
              0 => 'female',
              1 => 'male',
              2 => 'nn',
             ),
             ));
        $this->hasColumn('username', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('firstname', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('lastname', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('email', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '255',
             ));
        $this->hasColumn('language', 'string', 5, array(
             'type' => 'string',
             'default' => 'en_GB',
             'length' => '5',
             ));
        $this->hasColumn('password', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('salt', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('status', 'enum', null, array(
             'type' => 'enum',
             'values' => 
             array(
              0 => 'inactive',
              1 => 'active',
              2 => 'locked',
              3 => 'deleted',
             ),
             'default' => 'inactive',
             ));
        $this->hasColumn('role_id', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => '2',
             ));

        $this->option('collate', 'utf8_general_ci');
        $this->option('charset', 'utf8');
        $this->option('type', 'INNODB');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('User_Model_Role as Role', array(
             'local' => 'role_id',
             'foreign' => 'id'));

        $this->hasMany('Core_Model_Preference', array(
             'local' => 'id',
             'foreign' => 'user_id'));

        $this->hasMany('User_Model_Role', array(
             'local' => 'id',
             'foreign' => 'id'));

        $this->hasMany('User_Model_Preference', array(
             'local' => 'id',
             'foreign' => 'user_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $softdelete0 = new Doctrine_Template_SoftDelete();
        $this->actAs($timestampable0);
        $this->actAs($softdelete0);
    }
}