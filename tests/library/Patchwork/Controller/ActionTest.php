<?php

require_once(dirname(dirname(dirname(dirname(__FILE__))))).'/bootstrap.php';
/**
 *
 *
 *
 */
class Patchwork_Controller_ActionTest extends ControllerTestCase
{
    public function test_getDoctrineReturnsDoctrineConnection()
    {
        $this->dispatch();
    }
}