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
class Patchwork_ViewTest extends ControllerTestCase
{
    public function testPatchworkViewIsUsed()
    {
        $viewResource = $this->getContainer()->view;
        $this->assertInstanceOf('Patchwork_View', $viewResource);
    }
    
    public function testEscapeHelperIsNotLoaded()
    {
        $this->dispatch('/');
        $this->assertNotContains(
            'Zend_View_Helper_Escape',
            get_declared_classes()
        );
    }

    public function testUrlHelperIsNotLoaded()
    {
        $this->dispatch('/');
        $this->assertNotContains(
            'Zend_View_Helper_Url',
            get_declared_classes()
        );
    }
}