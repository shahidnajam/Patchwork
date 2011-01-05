<?php
/**
 * Simple storage service abstraction
 *
 * @author     Daniel Pozzi
 * @package    Patchwork
 * @subpackage Storage
 */
interface Patchwork_Storage_Service
{
    /**
     * @return array
     */
    function fetch(
        $model,
        array $where = null,
        $order = null,
        $limit = 0,
        $offset = null
    );

    /**
     * @return object|null
     */
    function find(
        $model,
        $pk
    );

    /**
     * implement fluent interface!
     * 
     * @return Patchwork_Storage_Service
     */
    function setObjectValues($object, array $values);

    /**
     * save an object
     */
    function save($object);

    /**
     * delete an object or in a table
     * 
     */
    function delete(
         $mixed,
         array $where = null
    );

    /**
     * exceptions error messages etc
     * 
     * @return string
     */
    function getErrorMessage();
}
