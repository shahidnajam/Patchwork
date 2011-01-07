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
        $this->getContainer()->set('Zend_Acl', $acl);
        $this->getContainer()->bindImplementation(
            'Patchwork_Controller_Plugin_Auth',
            'Patchwork_Controller_Plugin_Auth_Basic'
        );
        $this->auth = $this->getContainer()->Patchwork_Controller_Plugin_Auth;
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
        $this->getContainer()->Patchwork_DBAuthenticator->authenticate('dpozzi@gmx.net', 'test');
        $role = $this->auth->getUserRole();
        $this->assertEquals('admin', $role);
    }

    /**
     * @todo works on hudson only if false auth tried
     */
    public function testDefaultUserRoleIsGuest()
    {
        $this->getContainer()->Patchwork_DBAuthenticator->authenticate('dpozzi@gmx.net', 'not');
        $role = $this->auth->getUserRole();
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
        $request = $this->getRequest()->setModuleName('howto')
            ->setControllerName('index')
            ->setActionName('index');
        $this->assertTrue($this->auth->isAllowedRequest($request));

        $this->provideAuthenticatedUser();
        $request = $this->getRequest()->setModuleName('howto')
            ->setControllerName('forusers')
            ->setActionName('index');

        $this->assertTrue($this->auth->isAllowedRequest($request));
    }

    public function testRequestIsAllowedWithTwoConfigs()
    {
        $this->_getConfig2()->addConfigToAcl($this->getContainer()->Zend_Acl);
        $this->provideAuthenticatedUser();
        $request = $this->getRequest()->setModuleName('howto')
            ->setControllerName('index')
            ->setActionName('index');

        $this->assertTrue($this->auth->isAllowedRequest($request));

        $request = $this->getRequest()->setModuleName('user')
            ->setControllerName('forusers')
            ->setActionName('index');

        $this->assertTrue($this->auth->isAllowedRequest($request));
    }

    public function testNotRegisteredRoleIsForbidden()
    {
        $request = $this->getRequest()
            ->setModuleName('admin')
            ->setControllerName('index');

        $this->assertFalse($this->auth->isAllowedRequest($request));
    }

    public function testNotRegisteredResourceIsForbidden()
    {
        $request = $this->getRequest()
            ->setModuleName('admin')
            ->setControllerName('index');

        $this->assertFalse($this->auth->isAllowedRequest($request));
    }

    public function testRequestIsForbiddenForGuest()
    {
        $request = $this->getRequest()
            ->setModuleName('howto')
            ->setControllerName('forusers');

        $this->assertFalse($this->auth->isAllowedRequest($request));
    }
}
