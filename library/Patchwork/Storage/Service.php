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
     * find one row which matches conditions
     * 
     * @param string $model
     * @param array  $where conditions, assoc
     */
    function findWhere($model, array $where);

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
     * execute sql
     *
     * @param string $sql       sql query string
     * @param array  $params    query params
     * @param string $modelName class name of the model(s) to fetch
     *
     * @return mixed result
     */
    function query($sql, array $params = NULL, $modelName = NULL);

    /**
     * exceptions error messages etc
     * 
     * @return string
     */
    function getErrorMessage();
}
