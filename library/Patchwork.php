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
    const CONFIG_REGISTRY_KEY = 'config';

    /**
     * acl registry key
     */
    const ACL_REGISTRY_KEY = 'Zend_Acl';

    /**
     * Zend_Navigation
     */
    const BOOTSTRAP_NAVIGATION_KEY = 'navigation';

    /**
     * Zend_Session
     */
    const SESSION_REGISTRY_KEY = 'Zend_Session';
}