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
        Patchwork_Acl::factory(
            Patchwork_Acl::GUEST_ROLE,
            dirname(__FILE__) . '/acl.ini'
        );
        
        $request = new Zend_Controller_Request_Http;
        $auth = new Patchwork_Controller_Plugin_Auth;
        $auth->dispatchLoopStartup($request);

        $this->assertEquals(
            Zend_Registry::get(Patchwork::CONFIG_REGISTRY_KEY)
                ->patchwork->options->acl->errorController,
            $request->getControllerName()
            );
        $this->assertEquals(
            Zend_Registry::get(Patchwork::CONFIG_REGISTRY_KEY)
                ->patchwork->options->acl->errorAction,
            $request->getActionName()
            );
    }
}
