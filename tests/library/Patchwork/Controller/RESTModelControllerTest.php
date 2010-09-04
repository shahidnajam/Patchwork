<?php

require_once(dirname(dirname(dirname(dirname(__FILE__))))).'/bootstrap.php';
/**
 *
 *
 *
 */
class Patchwork_Controller_RESTModelControllerTest extends ControllerTestCase
{
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
     * disable http auth plugin
     */
    protected function _disableHttpAuth()
    {
        $front =  Zend_Controller_Front::getInstance();
        if($front->hasPlugin('Patchwork_Controller_Plugin_HttpAuth'))
            $front->unregisterPlugin('Patchwork_Controller_Plugin_HttpAuth');
    }

    /**
     * test for doctrine controller helper
     */
    public function testInit()
    {
        $controller = $this->_getController();
        $controller->init();

        $this->assertTrue(
            Zend_Controller_Action_HelperBroker::hasHelper('Doctrine')
            );
    }

    public function testInitModel()
    {
        $controller = $this->_getController();
        $res = $controller->initModel(false);
        $this->assertTrue($res instanceof User);
    }
    
    /**
     * 
     */
    public function testInitModel1()
    {
        $controller = $this->_getController();
        $res = $controller->initModel(1);
        $this->assertTrue($res instanceof User);
    }

    /**
     * test index/list request
     *
     */
    public function testIndex()
    {
        $this->_disableHttpAuth();
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
        $this->_disableHttpAuth();
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
        $this->_disableHttpAuth();
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
        $this->_disableHttpAuth();
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
        $this->_disableHttpAuth();
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
        $this->_disableHttpAuth();
        $this->dispatch(
            'api/user',
            'POST',
            array('username' => 'test', 'email' => 'test@123.com')
        );

        $this->assertModule('api');
        $this->assertController('user');
        $this->assertAction('post');
        $this->assertResponseCode(201);
    }

    /**
     * put test
     *
     *
     * 
     */
    public function testPutAction()
    {
        $this->_disableHttpAuth();
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
        Zend_Registry::set(Patchwork::ACL_REGISTRY_KEY, new Zend_Acl);
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
        $this->_disableHttpAuth();

        $payload = array('id' => 1);
        $this->dispatch('api/user', 'DELETE', $payload);

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
