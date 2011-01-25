<?php

require_once(dirname(dirname(dirname(dirname(__FILE__))))).'/bootstrap.php';
/**
 *
 *
 *
 */
class Patchwork_Error_HandlerTest extends ControllerTestCase
{
    /**
     *
     * @var Patchwork_Error_Handler
     */
    private $handler;
    
    public function setUp()
    {
        parent::setUp();
        $this->handler = $this->getContainer()
            ->getInstance(Patchwork::ERROR_HANDLER);
    }

    public function  tearDown()
    {
        parent::tearDown();
        $this->handler->unregister();
    }

    /**
     * @expectedException ErrorException
     */
    public function testRegistersAndThrowsErrorException()
    {
        $this->handler->registerAsErrorHandler();
        strpos();
    }
}