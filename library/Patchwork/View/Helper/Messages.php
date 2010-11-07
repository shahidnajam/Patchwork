<?php
/**
 * Patchwork
 *
 * @category    Library
 * @package     View
 * @subpackage  Helpers
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * Patchwork_View_Helper_Messages
 *
 * Helper to render messages form Patchwork_Messenger or Zends
 * FlashMessenger. Renders divs with status css classed as passed to the
 * messenger
 *
 * @category    Library
 * @package     View
 * @subpackage  Helpers
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_View_Helper_Messages extends Zend_View_Helper_Abstract
{
    /**
     * blueprint statuses
     */
    const ERROR  = 'error';
    const NOTICE = 'notice';
    const ADDED  = 'added';
    const SUCCES = 'success';
    const REMOVED= 'removed';

    /**
     * get messages from flash messenger
     * @return array
     */
    private function getMessages()
    {
        $messages = Zend_Controller_Action_HelperBroker::getStaticHelper(
                'FlashMessenger')->getMessages();
        $currentMessages = Zend_Controller_Action_HelperBroker::getStaticHelper(
                'FlashMessenger')->getCurrentMessages();

        return array_merge($messages, $currentMessages);
    }

    /**
     * flashMessages function.
     *
     * @param $translator An optional instance of Zend_Translate
     * @return string HTML of output messages
     */
    public function flashMessages($translator = NULL)
    {
        $messages = $this->getMessages();
        if (count($messages) == 0) {
            return;
        }

        $statMessages = array();
        $output = '';

        foreach ($messages as $message) {
            if (is_array($message)) {
                $status = key($message);
                $message= current($message);
            } else {
                $status = self::NOTICE;
            }

            if ($message == '') {
                continue;
            }

            if (!array_key_exists($status, $statMessages)) {
                $statMessages[$status] = array();
            }

            array_push($statMessages[$status], $message);
        }

        foreach ($statMessages as $status => $messages) {
            $output .= '<div class="' . $status . '"><ul>';
            foreach ($messages as $message) {
                $output .= '<li>' . $message . '</li>' . PHP_EOL;
            }
            $output .= '</ul></div>' . PHP_EOL;
        }

        return $output;
    }

    /**
     * the fm
     *
     * @return Zend_Controller_Action_Helper_FlashMessenger
     */
    public static function getFlashMessenger()
    {
        return
        Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
    }

    /**
     * add an error to the flash messenger
     *
     * @param string $message error message
     * @return void
     */
    public static function addError($message)
    {
        self::getFlashMessenger()->addMessage(array(self::ERROR => $message));
    }
}