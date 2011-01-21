<?php
require_once(dirname(dirname(dirname(dirname(__FILE__))))).'/tests/bootstrap.php';


class UserTest extends ControllerTestCase
{
    /**
     *
     */
    public function testContainsFields()
    {
        $user = new User;
        $result = $user->getIgnoredColumns();

        $expected = array(
            'deleted_at',
            'password',
            'salt',
        );

        $this->assertEquals($expected, $result);
    }

    
    /**
     *
     * 
     */
    public function testIsArray()
    {
        $user = new User;
        $result = $user->getIgnoredColumns();

        $this->assertTrue( is_array($result), 'Ist kein Array.' );
    }

    /**
     * @dataProvider nameIdProvider
     *
     */
    public function testUsername($id, $expected)
    {
        $user = Doctrine::getTable('User')->find($id);
        $this->assertTrue ($user->username == $expected );
    }

    /**
     * 
     */
    public function nameIdProvider()
    {
        return array(
            array(1, 'bonndan'),
            array(2, 'alex'),
            //array(3, 'youssef'),
        );
    }

    public function testGetRoleId()
    {
        $user = new User;
        $user->role = 'guest';
        $this->assertTrue($user->getRoleid() == 'guest');
    }

    public function testGetAuthenticatedUser()
    {
        $this->getContainer()->Patchwork_Auth_DBAuthenticator->authenticate('dpozzi@gmx.net', 'test');
        $res = User::getAuthenticatedUser();
        $this->assertTrue($res->id == 1);
    }
}