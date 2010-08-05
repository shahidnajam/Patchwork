<?php
/**
 * Patchwork
 *
 * @package    Patchwork
 * @subpackage Authentication
 * @author     Daniel Pozzi
 */

/**
 * Patchwork_Auth_RoleIdentity
 *
 * Identity having a role
 *
 * @package    Patchwork
 * @subpackage Authentication
 * @author     Daniel Pozzi
 */
interface Patchwork_Auth_RoleIdentity
{
    /**
     * returns the role descriptor
     *
     * @return string role name
     */
    public function getRole();
}