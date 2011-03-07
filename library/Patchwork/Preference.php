<?php
/**
 * Patchwork_Preference
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage Preference
 * 
 */
interface Patchwork_Preference
{
    /**
     * set a preference value
     * 
     * @param mixed $value value to set
     */
    function setValue($value);

    /**
     * get the value
     */
    function getValue();

    function equals($to);
}