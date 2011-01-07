<?php
/**
 * Storage Service implementation for the Plain Object Mapper
 *
 * @link https://github.com/bonndan/POM
 */
class Patchwork_Storage_Service_POM implements Patchwork_Storage_Service
{
    /**
     * POM instance
     * @var POM
     */
    private $pom;

    /**
     * constructor needs a POM instance
     * @param POM $pom 
     */
    public function  __construct(POM $pom)
    {
        $this->pom = $pom;
    }

    public function  find($model, $pk)
    {
        return $this->pom->find($model, $pk);
    }

    /**
     * save
     * 
     * @param object $object
     * @return boolean
     */
    public function save($object)
    {
        return $this->pom->save($object);
    }

    /**
     * fetch
     * @param <type> $model
     * @param array $where
     * @param <type> $order
     * @param <type> $limit
     * @param <type> $offset
     * @return <type> 
     */
    public function fetch($model, array $where = null, $order = NULL, $limit = 0, $offset = null) {

        return $this->pom->select($model, $where, $order, $limit, $offset);
    }

    /**
     * set values
     * 
     * @param object $object
     * @param array $values
     * @return Patchwork_Storage_Service_POM 
     */
    public function setObjectValues($object, array $values)
    {
        foreach ($values as $key => $value) {
            $object->$key = $value;
        }

        return $this;
    }

    /**
     * execute sql with params
     * 
     * @param string $sql
     * @param array  $params
     * @param string $modelName
     * @return array
     */
    public function query($sql, array $params = NULL, $modelName = NULL)
    {
        return $this->pom->fetchQuery($sql, $params, $modelName);
    }
}