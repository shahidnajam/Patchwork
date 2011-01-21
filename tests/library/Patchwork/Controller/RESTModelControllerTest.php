<?php

require_once(dirname(dirname(dirname(dirname(__FILE__))))).'/bootstrap.php';
/**
 *
 *
 *
 */
class Patchwork_Controller_RESTModelControllerTest extends ControllerTestCase
{
    public function setUp()
    {
        parent::setUp();
        $acl = new Zend_Acl;
        $ini = new Patchwork_Acl_Ini(dirname(__FILE__) .'/acl.ini');
        $ini->addConfigToAcl($acl);
        
        $this->getContainer()->set('Zend_Acl', $acl);
    }

    /**
     *
     * @return TestController
     */
    protected function _getController()
    {
        $controller = new TestController(
            new Zend_Controller_Request_Http,
            new Zend_Controller_Response_Http
        );

        return $controller;
    }

    /**
     * test index/list request
     *
     */
    public function testIndex()
    {
        $this->dispatch('api/user');

        $this->assertAction('index');
        $this->assertResponseCode(200);
        $body = json_decode($this->getResponse()->getBody());
        $this->assertEquals(2, count($body));
        $this->assertEquals('bonndan', $body[0]->username);
    }

    /**
     * test index/list request with suername search
     *
     */
    public function testIndexWithSearch()
    {
        $this->dispatch('api/user', 'GET', array('username' => 'bon'));

        $this->assertAction('index');
        $this->assertResponseCode(200);
        $body = json_decode($this->getResponse()->getBody());
        $this->assertEquals(1, count($body));
        $this->assertEquals('bonndan', $body[0]->username);
    }

    /**
     * test index with limit
     *
     */
    public function testIndexWithLimit()
    {
        $this->dispatch('api/user', 'GET', array('limit'=>1));

        $this->assertAction('index');
        $this->assertResponseCode(200);
        $body = json_decode($this->getResponse()->getBody());
        
        $this->assertEquals(1, count($body));
        $this->assertEquals('bonndan', $body[0]->username);
    }

    /**
     * test index with limit
     *
     */
    public function testIndexWithOffset()
    {
        $this->dispatch('api/user', 'GET', array('offset'=>1,'limit' => 1));

        $this->assertAction('index');
        $this->assertResponseCode(200);
        $body = json_decode($this->getResponse()->getBody());
        $this->assertEquals(1, count($body));
        $this->assertEquals('alex', $body[0]->username);
    }

    /**
     *
     */
    public function testGET()
    {
        $this->dispatch('api/user', 'GET', array('id' => 1));

        $this->assertAction('get');
        $this->assertResponseCode(200);
        $body = json_decode($this->getResponse()->getBody());
        $this->assertTrue(is_object($body));
        $this->assertEquals('bonndan', $body->username);
    }

    /**
     * test that postAction is excuted
     *
     * 
     */
    public function testPOST()
    {
        $this->dispatch(
            'api/user',
            'POST',
            array('username' => 'test', 'email' => 'test@123.com')
        );

        $this->assertModule('api');
        $this->assertController('user');
        $this->assertAction('post');
        $this->assertResponseCode(201, $this->getResponse()->getBody());
    }

    /**
     * put test
     *
     *
     * 
     */
    public function testPutAction()
    {
        $payload = array('id' => 1, 'email' => 'test@123.com');
        $this->dispatch('api/user', 'PUT', $payload);
        $this->assertAction(
            'put',
            Zend_Controller_Front::getInstance()->getResponse()->getBody()
        );
        $this->assertController('user');
        
        $this->assertResponseCode(
            200,
            'Response code: '.$this->getResponse()->getHttpResponseCode()
        );
        $response = $this->getResponse()->getBody();
        $this->assertContains(
            '"username":"bonndan"',
            $response
        );
    }

    /**
     * delete test
     *
     * 
     */
    public function testUnauthDeleteAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->getContainer()->bindFactory('Zend_Acl', 'Patchwork_Factory', 'acl');
        $plugin = $this->getContainer()
            ->getInstance('Patchwork_Controller_Plugin_Auth_HttpBasic');
        $plugin->setModule('api');
        Zend_Controller_Front::getInstance()->registerPlugin($plugin);
        
        $payload = array('id' => 1);
        $this->dispatch('api/user', 'DELETE', $payload);
        $this->assertAction('denied');
        $this->assertResponseCode(403);
    }

    /**
     * delete test
     *
     *
     */
    public function testDeleteAction()
    {
        $this->provideAuthenticatedUser();
        $payload = array('id' => 1);
        $this->dispatch('api/user', 'DELETE', $payload);

        $this->assertAction('delete');
        $this->assertResponseCode(
            204
        );
        /**
         * user entry deleted?
         */
        $user = Doctrine::getTable('User')->find(1);
        $this->assertFalse($user);

        
    }
}

/**
 * Controller to test
 */
class TestController extends Patchwork_Controller_RESTModelController
{
    public $modelName = 'User';
}
