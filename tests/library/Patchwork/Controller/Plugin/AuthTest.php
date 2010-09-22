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
     * 
     */
    public function testGetUserRoleOfAdmin()
    {
        User::authenticate('dpozzi@gmx.net', 'test');
        $role = Patchwork_Controller_Plugin_Auth::getUserRole();
        $this->assertEquals('admin', $role);
    }

    /**
     *
     */
    public function testDefaultUserRoleIsGuest()
    {
        //User::authenticate('dpozzi@gmx.net', 'not');
        $role = Patchwork_Controller_Plugin_Auth::getUserRole();
        $this->assertEquals('guest', $role);
    }

    /**
     * 
     */
    public function testDispatchLoopStartup()
    {
        Zend_Registry::set(Patchwork::ACL_REGISTRY_KEY, new Zend_Acl);
        $request = new Zend_Controller_Request_Http;
        $auth = new Patchwork_Controller_Plugin_Auth;
        $auth->dispatchLoopStartup($request);

        $this->assertEquals(
            Patchwork_Controller_Plugin_Auth::ERROR_CONTROLLER,
            $request->getControllerName()
            );
        $this->assertEquals(
            Patchwork_Controller_Plugin_Auth::ERROR_ACTION,
            $request->getActionName()
            );
    }
}
