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
            'created_at',
            'deleted_at',
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

    public function testX()
    {
        $this->markTestIncomplete();
    }
}