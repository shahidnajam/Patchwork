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
 * View_Helper_Messages
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
     * get messages from flash messenger (merged current and session)
     * 
     * @return array
     */
    private function getMessages()
    {
        $messenger = Patchwork_Messenger::getFlashMessenger();

        return array_merge(
            $messenger->getMessages(),
            $messenger->getCurrentMessages()
        );
    }

    /**
     * flashMessages function.
     *
     * @param $translator An optional instance of Zend_Translate
     * @return string HTML of output messages
     */
    public function Messages($returnArray = false)
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
            $output .= '<div class="' . $status . '">';
            foreach ($messages as $message) {
                $output .= $message.' ';
            }
            $output .= '</div>' . PHP_EOL;
        }

        return $output;
    }
}
