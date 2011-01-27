<?php
/**
 * Periodically changing passwords
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage UserAccount
 * @author     Daniel Pozzi
 */
interface Patchwork_UserAccount_PeriodicalPassword
{
    /**
     * get the timestamp of the date the password was changed
     * 
     * @return int timestamp
     */
    function getLastPasswordChangeTimestamp();

    /**
     * check if password change is within validity period
     * 
     * @return boolean
     */
    function isPasswordStillValid();
}