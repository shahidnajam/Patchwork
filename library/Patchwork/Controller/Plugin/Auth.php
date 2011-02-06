<?php
/**
 * Patchwork
 *
 * @category    Library
 * @package     Patchwork
 * @subpackage  Plugins
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * Auth Controller Plugin Interface
 *
 * @category    Library
 * @package     Patchwork
 * @subpackage  Plugins
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */
interface Patchwork_Controller_Plugin_Auth
{
    const GUEST_ROLE = 'guest';
    
    /**
     * the role of the current user
     * @return string
     */
    function getUserRole();

    /**
     * @return boolean
     */
    function isAllowedRequest(Zend_Controller_Request_Abstract $request);
}