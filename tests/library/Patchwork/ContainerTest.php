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
        $this->assertType('Patchwork_Container', $container);

        $this->assertEquals($container->Patchwork_Container, $container);
    }

    /**
     *
     */
    public function testConstructorWithOptions()
    {
        $container = new Patchwork_Container($this->options);
        $this->assertType('Patchwork_Container', $container);

        $binds = $container->getBindings();
        $this->assertContains('Patchwork_Controller_Plugin_Auth', array_keys($binds));
        $this->assertEquals('Patchwork_Controller_Plugin_Auth_Basic', $binds['Patchwork_Controller_Plugin_Auth']);
    }

    public function testGetBootStrapContainer()
    {
        $this->assertType('Patchwork_Container',
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
        $this->assertTrue($container->doctrine instanceof Doctrine_Connection);
        $this->assertTrue($container->Doctrine instanceof Doctrine_Connection);
    }

    public function testCreateInstanceFromBinding()
    {
        $container = new Patchwork_Container;
        $container->bindFactory('Zend_Acl', 'Patchwork_Factory', 'acl');
        $container->bindImplementation('ABC', 'Patchwork_Controller_Plugin_Auth_Basic');

        $this->assertEquals($container, $container->__get('Patchwork_Container'));
        $this->assertType('Patchwork_Controller_Plugin_Auth_Basic', $container->ABC);
    }

    public function testCreateInstanceFromFactory()
    {
        $container = new Patchwork_Container;
        $container->bindFactory('Zend_Acl', 'Patchwork_Factory', 'acl');

        $this->assertType('Zend_Acl', $container->Zend_Acl);
    }

    /**
     * @expectedException Patchwork_Exception
     */
    public function testUnregisteredDependencyException()
    {
        $container = new Patchwork_Container;
        $fails = $container->notRegistered;
    }
}
