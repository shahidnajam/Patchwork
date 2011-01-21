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
        return $this->getContainer()->getDBAuthAdapter();
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
        $res = $this->getContainer()->getInstance('Patchwork_Auth_DBAuthenticator')
            ->authenticate('dpozzi@gmx.net', 'test');
        $this->assertTrue($res);
    }
}