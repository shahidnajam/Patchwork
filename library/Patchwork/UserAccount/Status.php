<?php
/**
 * Patchwork_UserAccount_Status
 *
 * User account status pattern to use on auth models
 *
 * @package    Patchwork
 * @subpackage UserAccount
 * @author     Daniel Pozzi
 * @todo status names and numbers
 */
interface Patchwork_UserAccount_Status
{
    const DELETED = 0;
    const UNLOCKED = 1;
    const CREATED = 10;
    const LOCKED = 20;
    
    function getStatus();

    function setStatus();

    function isStatusUnlocked();
}
