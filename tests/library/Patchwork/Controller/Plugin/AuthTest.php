<?php

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/bootstrap.php';

/**
 * test the auth plugin
 *
 *
 */
class Patchwork_Controller_Plugin_AuthTest extends ControllerTestCase
{
    /**
     * auth plugin
     * @var Patchwork_Controller_Plugin_Auth
     */
    public $auth;

    /**
     * prepares acl with first config and injects it inot container
     * 
     * @return Zend_Acl
     */
    public function setup()
    {
        parent::setUp();
        
        $acl = new Zend_Acl();
        $this->_getConfig()->addConfigToAcl($acl);
        $this->getContainer()->set('acl', $acl);
        $this->auth = new Patchwork_Controller_Plugin_Auth($this->getContainer());
    }

    /**
     * get config
     * @return Zend_Config_Ini
     */
    protected function _getConfig()
    {
        return new Patchwork_Acl_Ini(
            dirname(dirname(dirname(__FILE__))) . '/test.ini'
        );
    }

    /**
     * get config
     * @return Zend_Config_Ini
     */
    protected function _getConfig2()
    {
        return new Patchwork_Acl_Ini(
            dirname(dirname(dirname(__FILE__))) . '/test_1.ini'
        );
    }

    
    /**
     * 
     */
    public function testGetUserRoleOfAdmin()
    {
        User::authenticate('dpozzi@gmx.net', 'test');
        $role = Patchwork_Controller_Plugin_Auth::getUserRole();
        $this->assertEquals('admin', $role);
    }

    /**
     * @todo works on hudson only if false auth tried
     */
    public function testDefaultUserRoleIsGuest()
    {
        User::authenticate('dpozzi@gmx.net', 'not');
        $role = Patchwork_Controller_Plugin_Auth::getUserRole();
        $this->assertEquals('guest', $role);
    }

    /**
     * 
     */
    public function testDispatchLoopStartup()
    {
        $request = new Zend_Controller_Request_Http;
        $this->auth->dispatchLoopStartup($request);

        $this->assertEquals('error', $request->getControllerName());
        $this->assertEquals('accessdenied', $request->getActionName());
    }

    public function testRequestIsAllowed()
    {
        $auth = new Patchwork_Controller_Plugin_Auth($this->getContainer());

        $request = $this->getRequest()->setModuleName('howto')
            ->setControllerName('index')
            ->setActionName('index');
        $this->assertTrue($auth->isAllowedRequest('guest', $request));

        $request = $this->getRequest()->setModuleName('howto')
            ->setControllerName('forusers')
            ->setActionName('index');

        $this->assertTrue($auth->isAllowedRequest('user', $request));
    }

    public function testRequestIsAllowedWithTwoConfigs()
    {
        $this->_getConfig2()->addConfigToAcl($this->getContainer()->acl);

        $request = $this->getRequest()->setModuleName('howto')
            ->setControllerName('index')
            ->setActionName('index');

        $this->assertTrue($this->auth->isAllowedRequest('guest', $request));

        $request = $this->getRequest()->setModuleName('user')
            ->setControllerName('forusers')
            ->setActionName('index');

        $this->assertTrue($this->auth->isAllowedRequest('user', $request));
    }

    public function testNotRegisteredRoleIsForbidden()
    {
        $request = $this->getRequest()
            ->setModuleName('admin')
            ->setControllerName('index');

        $this->assertFalse($this->auth->isAllowedRequest('gg', $request));
    }

    public function testNotRegisteredResourceIsForbidden()
    {
        $request = $this->getRequest()
            ->setModuleName('admin')
            ->setControllerName('index');

        $this->assertFalse($this->auth->isAllowedRequest('guest', $request));
    }

    public function testRequestIsForbiddenForGuest()
    {
        $request = $this->getRequest()
            ->setModuleName('howto')
            ->setControllerName('forusers');

        $this->assertFalse($this->auth->isAllowedRequest('guest', $request));
    }
}
