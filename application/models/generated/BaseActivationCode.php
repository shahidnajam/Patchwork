<?php

/**
 * BaseActivationCode
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $activation_code
 * @property timestamp $activated
 * @property integer $user_id
 * @property User $User
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 6820 2009-11-30 17:27:49Z jwage $
 */
abstract class BaseActivationCode extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('user_activation_codes');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             'length' => '4',
             ));
        $this->hasColumn('activation_code', 'string', 32, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '32',
             ));
        $this->hasColumn('activated', 'timestamp', null, array(
             'type' => 'timestamp',
             ));
        $this->hasColumn('user_id', 'integer', 4, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => '4',
             ));

        $this->option('collate', 'utf8_general_ci');
        $this->option('charset', 'utf8');
        $this->option('type', 'INNODB');
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('User', array(
             'local' => 'user_id',
             'foreign' => 'id'));

        $timestampable0 = new Doctrine_Template_Timestampable(array(
             'created' => 
             array(
              'name' => 'created',
             ),
             'updated' => 
             array(
              'name' => 'modified',
             ),
             ));
        $softdelete0 = new Doctrine_Template_SoftDelete(array(
             'name' => 'deleted',
             ));
        $this->actAs($timestampable0);
        $this->actAs($softdelete0);
    }
}