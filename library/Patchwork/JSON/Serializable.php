<?php
/**
 * interface for objects that provide a method to be serialized
 * into JSON
 *
 * @author     Daniel Pozzi
 * @package    Patchwork
 * @subpackage JSON
 */
interface Patchwork_JSON_Serializable
{
    /**
     * @return string
     */
    function toJSON();
}