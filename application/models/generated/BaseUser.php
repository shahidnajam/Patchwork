<?php

/**
 * BaseUser
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property enum $gender
 * @property string $firstname
 * @property string $lastname
 * @property string $email
 * @property string $mobile_number
 * @property string $preferred_language
 * @property string $password
 * @property string $salt
 * @property enum $status
 * @property enum $role
 * @property Doctrine_Collection $ActivationCodes
 * @property Doctrine_Collection $LostPassword
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseUser extends Doctrine_Record
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
             'length' => '255',
             ));
        $this->hasColumn('mobile_number', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('preferred_language', 'string', 5, array(
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
        $this->hasColumn('role', 'enum', null, array(
             'type' => 'enum',
             'values' => 
             array(
              0 => 'guest',
              1 => 'user',
              2 => 'admin',
             ),
             'default' => 'guest',
             ));

        $this->option('collate', 'utf8_general_ci');
        $this->option('charset', 'utf8');
        $this->option('type', 'INNODB');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('ActivationCode as ActivationCodes', array(
             'local' => 'id',
             'foreign' => 'user_id'));

        $this->hasMany('LostPassword', array(
             'local' => 'id',
             'foreign' => 'user_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $softdelete0 = new Doctrine_Template_SoftDelete();
        $this->actAs($timestampable0);
        $this->actAs($softdelete0);
    }
}