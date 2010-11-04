<?php
/**
 * Patchwork
 *
 * @category    Library
 * @package     Views
 * @subpackage  Helpers
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * Patchwork_Messenger
 *
 * a convenience wrapper for Zends Flash Messenger, the method names correspond
 * to Blueprint css frameworks interaction classes
 *
 * @category    Library
 * @package     Views
 * @subpackage  Helpers
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_Messenger
{
    /**
     * blueprint statuses
     */
    const ERROR   = 'error';
    const NOTICE  = 'notice';
    const ADDED   = 'added';
    const SUCCESS = 'success';
    const REMOVED = 'removed';

    /**
     * get the zend flash messenger
     *
     * @return Zend_Controller_Action_Helper_FlashMessenger
     */
    public static function getFlashMessenger()
    {
        return Zend_Controller_Action_HelperBroker::getStaticHelper(
            'FlashMessenger'
        );
    }

    /**
     * add an error to the flash messenger
     *
     * @param string $message error message
     * @return void
     */
    public static function notice($message)
    {
        self::getFlashMessenger()->addMessage(array(self::NOTICE => $message));
    }

    /**
     * add an error to the flash messenger
     *
     * @param string $message error message
     * @return void
     */
    public static function error($message)
    {
        self::getFlashMessenger()->addMessage(array(self::ERROR => $message));
    }

    /**
     * add an error to the flash messenger
     *
     * @param string $message error message
     * @return void
     */
    public static function success($message)
    {
        self::getFlashMessenger()->addMessage(array(self::SUCCESS => $message));
    }

    /**
     * add an error to the flash messenger
     *
     * @param string $message error message
     * @return void
     */
    public static function added($message)
    {
        self::getFlashMessenger()->addMessage(array(self::ADDED => $message));
    }

    /**
     * add an error to the flash messenger
     *
     * @param string $message error message
     * @return void
     */
    public static function removed($message)
    {
        self::getFlashMessenger()->addMessage(array(self::REMOVED => $message));
    }
}