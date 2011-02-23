<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'bootstrap.php';

/**
 *
 *
 *
 */
class SetupTest extends ControllerTestCase
{

    /**
     * test that setup() has created sqlite db, tables and inserted one user
     */
    public function testUserFixureIsLoaded()
    {
        $users = Doctrine::getTable('User_Model_User')->findAll();
        $this->assertTrue($users instanceof Doctrine_Collection);
        $this->assertEquals(2, $users->count());
    }

}