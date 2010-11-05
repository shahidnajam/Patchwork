<?php

require_once(dirname(dirname(dirname(__FILE__)))) . '/bootstrap.php';

/**
 * Test Patchwork_Acl
 *
 *
 */
class Patchwork_AclTest extends Zend_Test_PHPUnit_ControllerTestCase
{
    /**
     * acl
     * @var Patchwork_Acl
     */
    public $acl;

    /**
     * get config
     * @return Zend_Config_Ini 
     */
    protected function _getConfig()
    {
        return new Patchwork_Acl_Ini(dirname(__FILE__) . '/Acl/test.ini');
    }

    /**
     * get config
     * @return Zend_Config_Ini
     */
    protected function _getConfig2()
    {
        return new Patchwork_Acl_Ini(dirname(__FILE__) . '/Acl/test_1.ini');
    }

    public function testRequestIsAllowed()
    {
        $this->acl = new Patchwork_Acl();
        $this->acl->addConfig($this->_getConfig());
        
        $request = $this->getRequest()->setModuleName('howto')
            ->setControllerName('index')
            ->setActionName('index');
        $this->assertTrue($this->acl->isAllowedRequest('guest', $request));

        $request = $this->getRequest()->setModuleName('howto')
            ->setControllerName('forusers')
            ->setActionName('index');

        $this->assertTrue($this->acl->isAllowedRequest('user', $request));
    }

    public function testRequestIsAllowedWithTwoConfigs()
    {
        $this->acl = new Patchwork_Acl();
        $this->acl->addConfig($this->_getConfig());
        $this->acl->addConfig($this->_getConfig2());

        $request = $this->getRequest()->setModuleName('howto')
            ->setControllerName('index')
            ->setActionName('index');

        $this->assertTrue($this->acl->isAllowedRequest('guest', $request));

        $request = $this->getRequest()->setModuleName('user')
            ->setControllerName('forusers')
            ->setActionName('index');

        $this->assertTrue($this->acl->isAllowedRequest('user', $request));
    }

    public function testNotRegisteredRoleIsForbidden()
    {
        $this->acl = new Patchwork_Acl();
        $this->acl->addConfig($this->_getConfig());

        $request = $this->getRequest()
            ->setModuleName('admin')
            ->setControllerName('index');

        $this->assertFalse($this->acl->isAllowedRequest('gg', $request));
    }

    public function testNotRegisteredResourceIsForbidden()
    {
        $this->acl = new Patchwork_Acl();
        $this->acl->addConfig($this->_getConfig());
        
        $request = $this->getRequest()
            ->setModuleName('admin')
            ->setControllerName('index');

        $this->assertFalse($this->acl->isAllowedRequest('guest', $request));
    }

    public function testRequestIsForbiddenForGuest()
    {
        $this->acl = new Patchwork_Acl();
        $this->acl->addConfig($this->_getConfig());
        $request = $this->getRequest()
            ->setModuleName('howto')
            ->setControllerName('forusers');

        $this->assertFalse($this->acl->isAllowedRequest('guest', $request));
    }

    public function testFactory()
    {
        $acl = Patchwork_Acl::factory();
        $this->assertType('Patchwork_Acl', $acl);

        $this->assertTrue($acl->has('default_index'));
        $this->assertTrue($acl->has('howto_index'));
    }
}