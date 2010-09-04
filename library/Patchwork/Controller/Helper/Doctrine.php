<?php
/**
 * Doctrine Helper
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage Controller
 */

/**
 * Controller helper to fetch Doctrine models
 * derived from CU_Controller_Action_Helper_Doctrine by Jani Hartikainen
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage Controller
 */
class Patchwork_Controller_Helper_Doctrine
    extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * default name of the primary key
     * @var string
     */
    public static $primaryKey = 'id';

    /**
     * standard redirect
     * @var array
     */
    protected $_redirect = array(
        'action' => 'index',
        'controller' => null,
        'module' => null
    );

    /**
     * helper accessor
     *
     * @param string $componentName name of the model to fetch
     * @param mixed  $primary       int or request param name
     *
     * @return self|Doctrine_Record
     * @throws Patchwork_Exception
     */
    public function direct($componentName = null,
                           $primary = null
    ){
        if($componentName == null)
            return $this;

        if($primary == NULL)
            $primary = self::$primaryKey;

        if(is_string($primary)){
            $primary = (int)$this->getRequest()->getParam($primary);
        }

        if(!intval($primary) || $primary == 0)
            throw new InvalidArgumentException ('Primary key must be an integer');

        return $this->getRecordOrException($componentName, $primary);
    }

    /**
     * Set the redirection target
     *
     * @param $action string action name
     * @param $controller string controller name
     * @param $module string module name
     *
     * @return CU_Controller_Action_Helper_Doctrine implements a fluent interface
     */
    public function setRedirect(
        $action,
        $controller = null,
        $module = null
    ) {
        $this->_redirect['action'] = $action;
        $this->_redirect['controller'] = $controller;
        $this->_redirect['module'] = $module;

        return $this;
    }

    /**
     * Fetch record from DB or redirect
     *
     * @param $model string Name of the model class
     * @param $id int the record's PK
     * @return Doctrine_Record
     */
    public function getRecordOrRedirect($model, $id) {
        $record = $this->getRecord($model, $id);
        if($record == false) {
            $this->getActionController()->getHelper('Redirector')->goto(
                $this->_redirect['action'],
                $this->_redirect['controller'],
                $this->_redirect['module']
            );
        }

        return $record;
    }

    /**
     * Fetch record from DB or throw a not found exception
     *
     * @param string $model Name of the model class
     * @param int    $id    the record's PK
     *
     * @return Doctrine_Record
     * @throws Patchwork_Exception
     */
    public function getRecordOrException($model, $id) {
        if(!$record = $this->getRecord($model, $id))
            throw new Patchwork_Exception ('Model not found '.$model.' '.$id);

        return $record;
    }

    /**
     * Fetch record from DB
     *
     * @param $model string Name of the model class
     * @param $id int the record's PK
     *
     * @return Doctrine_Record|null
     */
    public function getRecord($model, $id) {
        return Doctrine::getTable($model)->find($id);
    }

    /**
     * lists all models, or returns the doctrine_query for it
     *
     * @param string  $model model name
     * @param array   $where assoc array of where conditions
     * @param integer $limit limit
     * @param integer $offset offset
     * @param boolean $returnQuery
     *
     * @return Doctrine_Collection|Doctrine_Query
     */
    public function listRecords(
        $model,
        array $where = null,
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
        
        if(((int)$limit) > 0)
            $query->limit($limit);
        if(is_int($offset))
            $query->offset($offset);

        return $query->execute();
    }

    /**
     * renders json strings using special views
     *
     * @param Doctrine_Record|Doctrine_Collection $data
     *
     * @return string
     * @throws InvalidArgumentException
     * @todo fix json_encode missing fields from object
     */
    public function toJSON($data)
    {
        if($data instanceof Patchwork_Doctrine_Model_Renderable){
            $output = $data->toArray();
            foreach($data->getIgnoredColumns() as $field)
                unset($output[$field]);
            return json_encode((object)$output);
        } elseif ($data instanceof Doctrine_Record) {
            return json_encode((object)$data->toArray());
        } elseif($data instanceOf Doctrine_Collection){
            $return = array();
            foreach($data as $model){
                $return[] = $this->toJSON($model);
            }
            return "[". implode(',', $return).']'; //array entries come as json
        } else {
            throw new InvalidArgumentException(
                'Doctrine_Record or Doctrine_Collection required'
            );
        }
    }
}