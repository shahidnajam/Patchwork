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
     * create request
     * 
     * @param array $requestArgs request params
     *
     * @return Zend_Controller_Request_Http
     */
    protected function _getNewRequest(array $requestArgs = null, $method = 'GET')
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['HTTP_HOST'] = 'testhost';
        $request = new Zend_Controller_Request_Http;
        
        if(is_array($requestArgs))
            foreach($requestArgs as $key=>$value)
                $request->setParam($key, $value);
        
        return $request;
    }

    /**
     * get response object
     * @return Zend_Controller_Response_Http 
     */
    protected function _getNewResponse()
    {
        $response = new Zend_Controller_Response_Http;
        $response->headersSentThrowsException = false;
        return $response;
    }

    /**
     *
     * @param <type> $requestArgs
     *
     * @return TestController
     */
    protected function _getController(array $requestArgs = null, $method = 'GET')
    {
        $controller = new TestController(
            $this->_getNewRequest($requestArgs, $method),
            $this->_getNewResponse()
        );

        return $controller;
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
     *
     */
    public function testInitModelFromRequestId()
    {
        $args = array('id' => 1);
        $controller = $this->_getController($args);
        $res = $controller->initModel(true);
        $this->assertTrue($res instanceof User);
        $this->assertEquals(1, $res->id);
    }

    /**
     *
     */
    public function testInitModelFromPOSTRequestId()
    {
        $args = array('id' => 1);
        $controller = $this->_getController($args, 'POST');
        $res = $controller->initModel(true);
        $this->assertTrue($res instanceof User);
        $this->assertEquals(1, $res->id);
    }

    /**
     * 
     */
    public function testPutAction()
    {
        $payload = array('id' => 1, 'email' => 'test@123.com');

        $controller = $this->_getController($payload, 'PUT');
        $controller->init();
        $controller->putAction();
        $this->assertResponseCode(200);
        $this->assertContains(
            'user/1',
            $controller->getResponse()->__toString()
        );
    }
}

/**
 * Controller to test
 */
class TestController extends Patchwork_Controller_RESTModelController
{
    public $modelName = 'User';
}