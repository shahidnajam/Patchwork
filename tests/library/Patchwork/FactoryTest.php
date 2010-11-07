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
 * Acl ini test
 *
 * @package    Testing
 * @subpackage Test
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_FactoryTest extends ControllerTestCase
{

    public function testAclFactory()
    {
        $container = new Patchwork_Container();
        $container->bindFactory('acl', 'Patchwork_Factory', 'acl');

        $acl = Patchwork_Factory::acl();
        $this->assertType('Zend_Acl', $acl);
        $this->assertTrue($acl->has('default_index'));
        $this->assertTrue($acl->has('howto_index'));
    }

}
