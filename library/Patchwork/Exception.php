<?php

/**
 * Patchwork
 *
 * PHP 5.2
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage Exception
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * Patchwork Exception
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage Exception
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_Exception extends Zend_Exception
{

    /**
     * @return Patchwork_Exception
     */
    public static function ACLnotRegistered()
    {
        return new self(
            'ACL not found in registry under ' . Patchwork::ACL_REGISTRY_KEY
        );
    }

    /**
     * @return Patchwork_Exception
     */
    public static function missingAclConfig()
    {
        return new self(
            'Acl settings missing in configuration'
        );
    }
}