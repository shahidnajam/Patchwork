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
            ->getInstance('Patchwork_Error_Handler');
        if(!$this->handler instanceof Patchwork_Error_Handler) {
            throw new Exception('Could not register error handler');
        }
    }

    public function  tearDown()
    {
        parent::tearDown();
        if(!$this->handler instanceof Patchwork_Error_Handler) {
            throw new Exception('Could not register error handler');
        }
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