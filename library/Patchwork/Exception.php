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
     * @throws Patchwork_Exception
     */
    public static function ACLnotRegistered()
    {
        throw new self(
            'ACL not found in registry under ' . Patchwork::ACL_REGISTRY_KEY
        );
    }
}