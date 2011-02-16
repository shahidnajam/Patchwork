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
class Patchwork_MessengerTest extends ControllerTestCase
{
    /**
     *
     * @var Patchwork_Messenger
     */
    private $messenger;

    public function  setUp()
    {
        parent::setUp();
        $this->messenger = $this->getContainer()->getMessenger();
    }

    public function testgetFlashMessenger()
    {
        $this->assertInstanceOf(
            'Zend_Controller_Action_Helper_FlashMessenger',
            Patchwork_Messenger::getFlashMessenger()
        );
    }

    public function testNotice()
    {
        $this->messenger->notice('A notice');
        $messages = Patchwork_Messenger::getFlashMessenger()->getCurrentMessages();
        $this->assertContains(
            'A notice',
            $messages[0],
            serialize($messages)
        );
    }

    public function testError()
    {
        $this->messenger->error('An error');
        $messages = Patchwork_Messenger::getFlashMessenger()->getCurrentMessages();
        $this->assertContains(
            'An error',
            $messages[0]
        );
    }

    public function testSuccess()
    {
        $this->messenger->success('a Success');
        $messages = Patchwork_Messenger::getFlashMessenger()->getCurrentMessages();
        $this->assertContains(
            'a Success',
            $messages[0]
        );
    }

    public function testAdded()
    {
        $this->messenger->added('an addition');
        $messages = Patchwork_Messenger::getFlashMessenger()->getCurrentMessages();
        $this->assertContains(
            'an addition',
            $messages[0]
        );
    }

    public function testRemoved()
    {
        $this->messenger->added('a removal');
        $this->assertTrue($this->messenger->getFlashMessenger()->hasCurrentMessages());

        $messages = Patchwork_Messenger::getFlashMessenger()->getCurrentMessages();
        $this->assertContains(
            'a removal',
            $messages[0]
        );
    }
}