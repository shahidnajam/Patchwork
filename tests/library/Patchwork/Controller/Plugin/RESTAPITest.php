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
    public function setUp()
    {
        parent::setUp();
        $acl = new Zend_Acl;
        $ini = new Patchwork_Acl_Ini(dirname(dirname(__FILE__)) .'/acl.ini');
        $ini->addConfigToAcl($acl);

        $this->getContainer()->set('Zend_Acl', $acl);
    }

    /**
     * test that
     */
    public function testAPIModuleRouting()
    {
        $request = new Zend_Controller_Request_Http;
        $request->setModuleName('api');
        $request->setControllerName('users');
        $_SERVER['HTTP_HOST'] = 'testhost';

        $plugin = $this->getContainer()->getInstance(
            'Patchwork_Controller_Plugin_RESTAPI', true
        );
        $plugin->routeStartup($request);

        $this->dispatch('api/user', 'GET', array('id' => 1));
        $this->assertAction('get');
    }

    public function testAPIModuleRoutingPOST()
    {
        $request = new Zend_Controller_Request_Http;
        $request->setModuleName('api');
        $request->setControllerName('users');
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_HOST'] = 'testhost';

        $plugin = $this->getContainer()->getInstance(
            'Patchwork_Controller_Plugin_RESTAPI', true
        );
        $plugin->routeStartup($request);;

        $this->dispatch('api/user', 'POST');
        $this->assertAction('post');
    }

    public function testAPIModuleRoutingPUT()
    {
        $request = new Zend_Controller_Request_Http;
        $request->setModuleName('api');
        $request->setControllerName('users');
        $_SERVER['REQUEST_METHOD'] = 'PUT';
        $_SERVER['HTTP_HOST'] = 'testhost';

        $front =  Zend_Controller_Front::getInstance();

        $plugin = $this->getContainer()->getInstance(
            'Patchwork_Controller_Plugin_RESTAPI'
        );
        $plugin->routeStartup($request);

        $this->dispatch('api/user', 'PUT');
        $this->assertAction('put');
    }


    public function testOtherModuleNotAffected()
    {
        $request = new Zend_Controller_Request_Http;
        $request->setModuleName('api');
        $request->setControllerName('users');
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_HOST'] = 'testhost';

        $plugin = $this->getContainer()->getInstance(
            'Patchwork_Controller_Plugin_RESTAPI'
        );
        $plugin->routeStartup($request);

        $this->dispatch('index/index', 'POST');
        $this->assertAction('index');
    }
}