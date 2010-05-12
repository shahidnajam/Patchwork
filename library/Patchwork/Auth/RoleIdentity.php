<?php
/**
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