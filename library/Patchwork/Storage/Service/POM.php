<?php
/**
 * Storage Service implementation for the Plain Object Mapper
 *
 * @link https://github.com/bonndan/POM
 */
class Patchwork_Storage_Service_POM implements Patchwork_Storage_Service
{
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
}