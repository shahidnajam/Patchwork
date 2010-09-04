<?php
/**
 * Integration test
 * 
 * @category Testing
 * @package  Patchwork
 * @subpackage Plugin
 */
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/bootstrap.php';

/**
 * test the rest routing plugin
 *
 *
 */
class Patchwork_Controller_Plugin_RESTAPITest extends ControllerTestCase
{
    /**
     * test that
     */
    public function testAPIModuleRouting()
    {
        Zend_Registry::set(Patchwork::ACL_REGISTRY_KEY, new Zend_Acl);
        $request = new Zend_Controller_Request_Http;
        $request->setModuleName('api');
        $request->setControllerName('users');
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_HOST'] = 'testhost';

        $plugin = new Patchwork_Controller_Plugin_RESTAPI;
        $plugin->routeStartup($request);

        $this->dispatch('api/user', 'GET', array('id' => 1));
        $this->assertAction('get');

        $this->dispatch('api/user', 'POST');
        $this->assertAction('post');

        $this->dispatch('api/user', 'PUT');
        $this->assertAction('put');

        $this->dispatch('api/user', 'DELETE');
        $this->assertAction('delete');
    }

    public function testOtherModuleNotAffected()
    {
        Zend_Registry::set(Patchwork::ACL_REGISTRY_KEY, new Zend_Acl);
        $request = new Zend_Controller_Request_Http;
        $request->setModuleName('api');
        $request->setControllerName('users');
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_HOST'] = 'testhost';

        $plugin = new Patchwork_Controller_Plugin_RESTAPI;
        $plugin->routeStartup($request);

        $this->dispatch('index/index', 'POST');
        $this->assertAction('index');
    }
}