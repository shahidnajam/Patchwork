<?php

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/bootstrap.php';

/**
 * test the auth plugin
 *
 *
 */
class Patchwork_Controller_Plugin_FirewallTest extends ControllerTestCase
{
    /**
     * firewall
     * @var  Patchwork_Controller_Plugin_Firewall
     */
    public $firewall;

    public function setUp()
    {
        parent::setUp();
        $this->firewall = $this->getContainer()
            ->getInstance('Patchwork_Controller_Plugin_Firewall');
        $this->getContainer()->set('Zend_Acl', new Zend_Acl);
        Zend_Controller_Front::getInstance()
            ->unregisterPlugin('Patchwork_Controller_Plugin_Auth');
        Zend_Controller_Front::getInstance()
            ->registerPlugin($this->firewall);
    }

    public function testConstructor()
    {
        $this->assertType('Patchwork_Controller_Plugin_Firewall', $this->firewall);
    }

    public function testGetDispatch()
    {
        $this->getRequest()
            ->setControllerName('index')
            ->setActionName('firewall');
        $this->dispatch();
    }
}