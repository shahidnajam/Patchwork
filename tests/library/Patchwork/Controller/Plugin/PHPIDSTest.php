<?php

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/bootstrap.php';

/**
 * test the phpids plugin
 *
 *
 */
class Patchwork_Controller_Plugin_PHPIDSTest extends ControllerTestCase
{
    /**
     * @var Patchwork_Controller_Plugin_PHPIDS
     */
    private $plugin;

    /**
     * @var Zend_Controller_Request_Abstract
     */
    private $testRequest;

    public function  setUp()
    {
        parent::setUp();
        $this->plugin = new Patchwork_Controller_Plugin_PHPIDS;
        $this->testRequest = $this->getRequest()
            ->setModuleName('testmodule')
            ->setControllerName('testcontroller')
            ->setActionName('testaction');
    }
    
    public function testDefaultOperation()
    {
        $this->plugin->preDispatch($this->testRequest);

        $this->assertEquals('testmodule', $this->testRequest->getModuleName());
        $this->assertEquals('testcontroller', $this->testRequest->getControllerName());
        $this->assertEquals('testaction', $this->testRequest->getActionName());
    }

    public function testWithImpact()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['SERVER_ADDR'] = '127.0.0.1';
        $_REQUEST['bad'] = '<script>alert();</script>';
        $this->plugin->preDispatch(
            $this->testRequest
        );

        $this->assertEquals('error', $this->testRequest->getControllerName());
        $this->assertEquals('badrequest', $this->testRequest->getActionName());
    }

    public function testSkippedAction()
    {
        $this->plugin->preDispatch(
            $this->testRequest->setModuleName('mymodule')
            ->setControllerName('mycontroller')
            ->setActionName('myaction')
        );
        $_REQUEST['bad'] = '<script>alert();</script>';

        $this->assertEquals('mymodule', $this->testRequest->getModuleName());
        $this->assertEquals('mycontroller', $this->testRequest->getControllerName());
        $this->assertEquals('myaction', $this->testRequest->getActionName());
    }
}