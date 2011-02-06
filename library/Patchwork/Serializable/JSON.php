<?php
/**
 * interface for objects that provide a method to be serialized
 * into JSON
 *
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 * @package    Patchwork
 * @subpackage Serializable
 * @category   Library
 */
interface Patchwork_Serializable_JSON
{
    /**
     * returns a json representation
     * @return string
     */
    function toJSON();
}