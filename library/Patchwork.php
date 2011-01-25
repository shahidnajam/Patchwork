<?php

/**
 * Patchwork constants namespace
 *
 * @category   Application
 * @package    Patchwork
 * @subpackage Namespace
 */
class Patchwork
{
    /**
     * Patchwork Version
     */
    const VERSION = "0.1a";

    /**
     * where to find the config in the registry
     */
    const CONTAINER_CONFIG_KEY = 'config';

    /**
     * acl registry key
     */
    const ACL_REGISTRY_KEY = 'Zend_Acl';

    /**
     * Zend_Navigation
     */
    const CONTAINER_NAVIGATION_KEY = 'navigation';

    /**
     * Zend_Session
     */
    const SESSION_REGISTRY_KEY = 'Zend_Session';

     /**
     * where the config is stored: patchwork.options.acl
     */
    const CONTAINER_ACL_KEY = 'acl';
    /**
     * default role
     */
    const ACL_GUEST_ROLE = 'guest';

    const ERROR_HANDLER = 'Patchwork_Error_Handler';
}