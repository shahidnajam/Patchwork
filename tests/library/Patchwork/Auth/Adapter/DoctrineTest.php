<?php
/**
 *
 *
 *
 */
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/bootstrap.php';

/**
 *
 *
 *
 */
class Patchwork_Auth_Adapter_DoctrineTest extends ControllerTestCase
{
    protected function getAdapter()
    {
        $adapter = new Patchwork_Auth_Adapter_Doctrine(
            'users',
            'email',
            'password',
            'CONCAT(md5('
        );
        return $adapter;
    }

    /**
     *
     */
    public function testConstructor()
    {
        $adapter = $this->getAdapter();
        $this->assertTrue($adapter instanceof Patchwork_Auth_Adapter_Doctrine);
    }

    /**
     * 
     */
    public function testSetIdentityAndCredential()
    {
        $adapter = $this->getAdapter();
        $adapter->setIdentity('one')->setCredential('two');
    }

    /**
     *
     */
    public function testAuthenticate()
    {
        $res = User::authenticate('dpozzi@gmx.net', 'test');
        $this->assertTrue($res);
    }
}