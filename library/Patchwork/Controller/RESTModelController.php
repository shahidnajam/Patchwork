<?php
/**
 * REST Controller plus Doctrine
 *
 * make sure to enable rest routing
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage Controller
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 * @link http://techchorus.net/create-restful-applications-using-zend-framework-part-ii-using-http-response-code
 */
abstract class Patchwork_Controller_RESTModelController
    extends Zend_Rest_Controller
{
    /**
     * @var string
     */
    public $modelName;

    /**
     * PK of the model, used to automatically instantiate the model if in request
     * @var string
     */
    public $modelPrimaryKey = 'id';

    /**
     * model instance
     * @var object
     */
    public $model;

    /**
     * parse incoming json from cappuccinoResource
     * @var boolean
     */
    public $cappuccinoResourceCompatible = false;

    /**
     * request constants
     */
    const OFFSET_PARAM = 'offset';
    const LIMIT_PARAM  = 'limit';

    /**
     * disable views and layouts
     *
     * 
     */
    public function init()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $doctrine = new Patchwork_Controller_Helper_Doctrine($this);
        Zend_Controller_Action_HelperBroker::addHelper($doctrine);
    }
    
    /**
     * finds the model if PK is in request
     *
     * @param boolean|integer $withPrimaryKey can be pk or bool
     * 
     * @return Doctrine_Record
     * @throws Patchwork_Exception
     */
    public function initModel($withPrimaryKey = true)
    {
        if(!$withPrimaryKey)
            return new $this->modelName;

        if($withPrimaryKey === TRUE)
            $withPrimaryKey = (int)$this->_getParam($this->modelPrimaryKey);
        else
            $withPrimaryKey = (int)$withPrimaryKey;

        return $this->model = $this->_helper->Doctrine
            ->getRecordOrException(
            $this->modelName,
            $withPrimaryKey
        );
    }

    /**
     * renders json strings using special views 
     *
     * @param Doctrine_Record|Doctrine_Collection $data
     *
     * @return string
     * @throws InvalidArgumentException
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

    /**
     * parses incoming json, this is a special configuration for request sent
     * by CappuccinoResource: there is an extra key (=model name) describing the
     * data
     *
     * @param boolean $forModel pick subset and convert to array
     * 
     * @return object|array
     */
    protected function _getAllParams($forModel = true)
    {
        if(!$this->cappuccinoResourceCompatible)
            return $this->_getAllParams();
        
        $rawBody = $this->getRequest()->getRawBody();
        $all = json_decode($rawBody);
        if($forModel)
            return (array)$all->{strtolower($this->modelName)};
        else
            return $all;
    }

    /**
     * returns the get url for a model: /user/1
     *
     * @param Doctrine_Record $model
     * 
     * @return string
     */
    protected function getModelURL(Doctrine_Record $model)
    {
        $controller = $this->getRequest()->getControllerName();
        $url = $_SERVER['HTTP_HOST']
            . Zend_Controller_Front::getInstance()->getBaseUrl()
            . DIRECTORY_SEPARATOR . $controller
            . DIRECTORY_SEPARATOR . $model->{$this->modelPrimaryKey};

        return $url;
    }

    /**
     * list, the standard request
     *
     * 
     */
    public function indexAction()
    {
        $offset = (int)$this->_getParam(self::OFFSET_PARAM);
        $limit  = (int)$this->_getParam(self::LIMIT_PARAM);
        $objects = $this->_helper->Doctrine->listRecords(
            $this->modelName,
            $limit,
            $offset
        );
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json')
            ->appendBody(
                $this->toJSON($objects)
            );
    }

    /**
     * one object
     *
     *
     * @todo enable application/json header
     */
    public function getAction()
    {
        try {
            $this->initModel();
        } catch (Exception $e){
            $this->invalidAction();
        }

        if(!$this->model)
            return $this->_returnNotfound();

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json')
            ->appendBody(
                $this->toJSON($this->model)
            );
    }

    /**
     * put (update)
     *
     * @todo check resp code
     */
    public function putAction()
    {
        try {
            $this->initModel();
        } catch (Exception $e){
            $this->invalidAction();
        }

        if(!$this->model)
            return $this->_returnNotfound();
        
        $params = $this->_getAllParams();
        $model->fromArray($params);
        $model->save();
        
        $url = $this->getModelURL($model);

        $this->getResponse()
                ->setHttpResponseCode(200)
                ->setHeader("Location", $url)
                ->appendBody($this->toJSON($model));
    }

    /**
     * post (create), sends back the url
     *
     *
     */
    public function postAction()
    {
        $model = new $this->modelName;
        $params = $this->_getAllParams();
        $model->fromArray($params);
        $model->save();

        if($model->id) {
            $url = Zend_Controller_Front::getInstance()->getBaseUrl() 
                . DIRECTORY_SEPARATOR . $this->modelName
                . DIRECTORY_SEPARATOR . $model->id;

            $this->getResponse()
                ->setHttpResponseCode(201)
                ->setHeader("Location", $url)
                ->appendBody($this->toJSON($model));
        }
    }

    /**
     * delete
     *
     *
     */
    public function deleteAction()
    {
        try {
            $this->initModel();
        } catch (Exception $e){
            $this->invalidAction();
        }

        if(!$this->model)
            return $this->_returnNotfound();

        $this->model->delete();
        $this->getResponse()
            ->setHttpResponseCode(204);
    }

    /**
     * Invalid request
     *
     *
     */
    public function invalidAction()
    {
        $this->getResponse()->setHttpResponseCode(403);
    }

    /**
     * not found
     *
     *
     */
    protected function _returnNotfound()
    {
        return $this->getResponse()->setHttpResponseCode(404);
    }
}