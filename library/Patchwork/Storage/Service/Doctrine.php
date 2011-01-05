<?php
/**
 * Doctrine implementation of Patchwork Storage Service
 *
 * @author     Daniel Pozzi
 * @package    Patchwork
 * @subpackage Storage
 */
class Patchwork_Storage_Service_Doctrine implements Patchwork_Storage_Service
{
    private $errorMessage;
    
    /**
     * find ONE record
     * 
     * @param string $model
     * @param int    $pk
     * @return Doctrine_Record
     */
    public function find($model, $pk)
    {
        return Doctrine::getTable($model)->find($pk);
    }

    /**
     * set object values
     * 
     * @param object $object
     * @param array  $values
     */
    public function setObjectValues($object, array $values)
    {
        return $this->setRecordValues($object, $values);
    }

    /**
     *
     * @param Doctrine_Record $object
     * @param array $values
     * @return Patchwork_Storage_Service_Doctrine 
     */
    private function setRecordValues(Doctrine_Record $object, array $values)
    {
        foreach ($values as $key => $value) {
            try {
                $object->set($key, $value);
            } catch (Doctrine_Record_UnknownPropertyException $e) {
                //todo, check if object has field hasColumn did not work
            }
        }
        return $this;
    }

    /**
     * fetch
     *
     * @param string $model
     * @param array  $where
     * @param string $order
     * @param int    $limit
     * @param int    $offset
     * 
     * @return Doctrine_Collection
     */
    public function fetch(
        $model,
        array $where = null,
        $order = null,
        $limit = 0,
        $offset = null
    ) {
        $query = Doctrine::getTable($model)->getQueryObject();
        if(!$query)
            throw new Patchwork_Exception(
                'Could not query query object for ' . $model
            );

        if($where){
            foreach($where as $key => $cond)
                $query->where($key, $cond);
        }

        if ((int)$limit > 0) {
            $query->limit($limit);
        }
        if ($order) {
            $query->orderBy($order);
        }

        if(is_int($offset)) {
            $query->offset($offset);
        }

        $collection = $query->execute();
        if($collection->count() === 0) {
            return null;
        }

        $return = array();
        foreach ($collection as $record) {
            $return[] = $record;
        }
        return $return;
    }

    /**
     * save
     * 
     * @param Doctrine_Record $object
     * @return boolean
     */
    public function save($object)
    {
        try {
            $object->save();
            return true;
        } catch (Exception $e) {
            $this->errorMessage = $e->getMessage();
            return false;
        }
    }

    /**
     * delete
     * 
     * @param Doctrine_Record|string $mixed
     * @param array $where 
     */
    public function  delete($mixed, array $where = null)
    {
        if ($mixed instanceof Doctrine_Record) {
            $mixed->delete();
        } elseif (is_string($mixed)) {
            Doctrine::getTable($mixed)->getQueryObject()
                ->delete()
                ->where($where);
        }

        return true;
    }

    public function  getErrorMessage()
    {
        return $this->errorMessage;
    }
}