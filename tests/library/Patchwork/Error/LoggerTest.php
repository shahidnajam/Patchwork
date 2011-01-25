<?php

require_once(dirname(dirname(dirname(dirname(__FILE__))))).'/bootstrap.php';
/**
 *
 *
 *
 */
class Patchwork_Error_LoggerTest extends ControllerTestCase
{
    /**
     *
     * @var Patchwork_Error_Logger
     */
    private $logger;

    public function  setUp()
    {
        parent::setUp();
        $this->logger = $this->getContainer()
            ->getInstance('Patchwork_Error_Logger');
        $this->getContainer()->getInstance('Zend_Log')
            ->addWriter(array('writerName' => 'Mock'));
    }

    public function testHandleException()
    {
        $ex = new Exception('test');
        $this->logger->handleException($ex);
    }

    /**
     * 
     */
    public function testGetRefCode()
    {
        $ex = new Exception('test');
        $this->logger->handleException($ex);

        $ref = $this->logger->getRefCode();
        $this->assertEquals(9, strlen($ref));
        $this->assertEquals(':', $ref[4]);
    }
}