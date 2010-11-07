<?php

require_once(dirname(dirname(dirname(dirname(__FILE__))))).'/bootstrap.php';
/**
 *
 *
 *
 */
class Patchwork_Controller_ActionTest extends ControllerTestCase
{
    public function setUp()
    {
        parent::setUp();
        Patchwork_Container::getBootstrapContainer()
            ->bindFactory('Zend_Acl', 'Patchwork_Factory', 'acl');
    }
    
    public function test_getDoctrineReturnsDoctrineConnection()
    {
        $this->dispatch();
    }
}