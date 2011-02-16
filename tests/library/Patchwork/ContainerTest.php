<?php

/**
 * Patchwork
 *
 * @package    Testing
 * @subpackage Test
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
require_once((dirname(dirname(dirname(__FILE__))))) . '/bootstrap.php';

/**
 * Container test
 *
 * @package    Testing
 * @subpackage Test
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_ContainerTest extends ControllerTestCase
{

    public $options = array(
        'patchwork' => array(
            'container' => array(
                'Patchwork_Controller_Plugin_Auth' => array(
                    'bindImplementation' => 'Patchwork_Controller_Plugin_Auth_Basic'
                )
            )
        )
    );
    
    public function testContrustructor()
    {
        $container = new Patchwork_Container;
        $this->assertInstanceOf('Patchwork_Container', $container);

        $this->assertEquals($container->Patchwork_Container, $container);
    }

    /**
     *
     */
    public function testConstructorWithOptions()
    {
        $container = new Patchwork_Container($this->options);
        $this->assertInstanceOf('Patchwork_Container', $container);

        $binds = $container->getBindings();
        $this->assertContains('Patchwork_Controller_Plugin_Auth', array_keys($binds));
        $this->assertEquals('Patchwork_Controller_Plugin_Auth_Basic', $binds['Patchwork_Controller_Plugin_Auth']);
    }

    public function testGetBootStrapContainer()
    {
        $this->assertInstanceOf('Patchwork_Container',
            Patchwork_Container::getBootstrapContainer()
        );
    }

    public function testGetWithExistingImplementation()
    {
        $container = new Patchwork_Container;
        $this->assertEquals($container, $container->__get('Patchwork_Container'));
    }

    public function testGetWithRegisteredResourceIsLowerCase()
    {
        $container = Patchwork_Container::getBootstrapContainer();
        $this->assertTrue($container->doctrine instanceof ZFDoctrine_Registry);
    }

    public function testCreateInstanceFromBinding()
    {
        $container = new Patchwork_Container;
        $container->bindFactory('Zend_Acl', 'Patchwork_Factory', 'acl');
        $container->bindImplementation('ABC', 'Patchwork_Controller_Plugin_Auth_Basic');

        $this->assertEquals($container, $container->__get('Patchwork_Container'));
        $this->assertInstanceOf('Patchwork_Controller_Plugin_Auth_Basic', $container->ABC);
    }

    public function testCreateInstanceFromFactory()
    {
        $container = new Patchwork_Container;
        $container->bindFactory('Zend_Acl', 'Patchwork_Factory', 'acl');

        $this->assertInstanceOf('Zend_Acl', $container->Zend_Acl);
    }

    /**
     * @expectedException ErrorException
     */
    public function testUnregisteredDependencyException()
    {
        $container = new Patchwork_Container;
        $fails = $container->notRegistered;
    }

    public function testGetMessenger()
    {
        $container = new Patchwork_Container;
        $this->assertInstanceOf(
            'Patchwork_Messenger',
            $container->getMessenger()
        );
    }

    public function testGetDomainLogService()
    {
        $container = new Patchwork_Container;
        $container->bindImplementation(
            'Patchwork_Storage_Service',
            'Patchwork_Storage_Service_Doctrine'
        );
        $container->bindImplementation(
            'Patchwork_DomainLog_Model',
            'DLStub'
        );
        $this->assertInstanceOf(
            'Patchwork_DomainLog_Service',
            $container->getDomainLogService()
        );
    }

    public function testGetStorageService()
    {
        $container = new Patchwork_Container;
        $container->bindImplementation(
            'Patchwork_Storage_Service',
            'Patchwork_Storage_Service_Doctrine'
        );
        $this->assertInstanceOf(
            'Patchwork_Storage_Service',
            $container->getStorageService()
        );
    }

    public function testGetAppConfig()
    {
        $container = Patchwork_Container::getBootstrapContainer();
        $this->assertInstanceOf(
            'Zend_Config',
            $container->getApplicationConfig()
        );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBindImplementationException()
    {
        $container = new Patchwork_Container;
        $container->bindImplementation('An_Interface', new stdClass());
    }

    public function testGetFormFactory()
    {
        $this->assertInstanceOf(
            'Patchwork_Form_Factory',
            $this->getContainer()->getFormFactory()
        );
    }

    public function testApplicationConfig()
    {
        $this->assertInstanceOf(
            'Zend_Config',
            $this->getContainer()->getApplicationConfig()
        );
    }

    public function testGetDBAuthAdapter()
    {
        $this->assertInstanceOf(
            'Patchwork_Auth_DBAdapter',
            $this->getContainer()->getDBAuthAdapter()
        );
    }
}

class DLStub implements Patchwork_DomainLog_Model
{
    public function  __construct(Patchwork_Storage_Service $service)
    {
    }

    public function  createFromEvent(array $event)
    {
    }
}