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
    protected function _getConfig($role = 'guest')
    {
        return new Patchwork_Acl_Ini(dirname(__FILE__) . '/acltest.ini', $role);
    }

    public function testConfig()
    {
        $config = $this->_getConfig('guest');
        $this->assertFalse($config->areAllSectionsLoaded());

        $this->assertTrue($config instanceof Zend_Config);
        $this->assertTrue($config->default instanceof Zend_Config);
        $this->assertEquals(1, $config->default->index);
        $this->assertTrue($config->user instanceof Zend_Config);

        
    }

    public function testGuestAcl()
    {
        $config = $this->_getConfig();
        $this->acl = new Patchwork_Acl($config);

        $this->assertTrue($this->acl->has('default'));
        $this->assertTrue($this->acl->has('user'));
        $this->assertTrue($this->acl->isAllowed('guest', 'default', 'index'));
        $this->assertFalse($this->acl->isAllowed('guest', 'default', 'x'));
    }

    public function testUserAcl()
    {
        $config = $this->_getConfig('user');
        $this->acl = new Patchwork_Acl($config);

        $this->assertTrue($this->acl->has('default'));
        $this->assertTrue($this->acl->has('user'));

        $this->assertTrue($this->acl->isAllowed('user', 'userstuff', 'index'));
        $this->assertFalse($this->acl->isAllowed('guest', 'userstuff', 'index'));
    }

    public function testRequestIsAllowed()
    {
        $this->acl = new Patchwork_Acl($this->_getConfig());
        $request = $this->getRequest()->setModuleName('default')
            ->setControllerName('index');

        $this->assertTrue($this->acl->isAllowedRequest('guest', $request));
    }

    public function testRequestIsForbidden()
    {
        $this->acl = new Patchwork_Acl($this->_getConfig());
        $request = $this->getRequest()
            ->setModuleName('admin')
            ->setControllerName('index');

        $this->assertFalse($this->acl->isAllowedRequest('guest', $request));
    }
}