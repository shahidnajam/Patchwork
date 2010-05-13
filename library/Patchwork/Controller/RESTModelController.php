<?php
/**
 * REST Controller plus Doctrine
 *
 * @package Patchwork
 * @link http://techchorus.net/create-restful-applications-using-zend-framework-part-ii-using-http-response-code
 */
abstract class Patchwork_Controller_RESTModelController
extends Zend_Rest_Controller
implements Patchwork_Controller
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
        
        $doctrine = new Patchwork_Controller_Action_Helper_Doctrine($this);
        Zend_Controller_Action_HelperBroker::addHelper($doctrine);
    }
    
    /**
     * finds the model if PK is in request
     *
     * @param boolean|integer $withPrimaryKey can be pk or bool
     * 
     * @return Doctrine_Record
     */
    public function initModel($withPrimaryKey = true)
    {
        if(!$withPrimaryKey)
            return new $this->modelName;

        if($withPrimaryKey === TRUE)
            $withPrimaryKey = $this->_getParam($this->modelPrimaryKey);

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
        if($data instanceof Doctrine_Record){
            return Patchwork_View_Helper_RenderModel::renderModel($data, 'json');
        } elseif($data instanceOf Doctrine_Collection){
            $return = array();
            foreach($data as $model){
                $return[] = Patchwork_View_Helper_RenderModel::renderModel(
                    $model, 'json'
                );
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
     * by CappuccinoResource: there is an extra key (=model name) describing the data
     *
     * @param $forModel pick subset and convert to array
     * 
     * @return object|array
     */
    protected function getJSONParams($forModel = true)
    {
        $rawBody = $this->getRequest()->getRawBody();
        $all = json_decode($rawBody);
        if($forModel)
            return (array)$all->{strtolower($this->modelName)};
        else
            return $all;
    }

    /**
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
     * list
     *
     * 
     */
    public function indexAction()
    {
        $this->_forward('get');
    }

    /**
     * listing
     *
     *
     * @todo enable application/json header
     */
    public function getAction()
    {
        $offset = $this->_getParam(self::OFFSET_PARAM);
        $objects = $this->_helper->Doctrine->listRecords($this->modelName);
        $this->getResponse()
            ->setHttpResponseCode(200)
            //->setHeader('Content-type', 'application/json')
            ->appendBody(
                $this->toJSON($objects)
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
            $model = $this->initModel();
        } catch (Exception $e) {
            $this->_forward('invalid');
        }
        $params = $this->getJSONParams();
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
        $params = $this->getJSONParams();
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
    public function deleteAction() {
        try {
            $model = $this->initModel();
        } catch (Exception $e) {
            $this->_forward('invalid');
        }
        $model->delete();
        $this->getResponse()
            ->setHttpResponseCode(204);
    }

    /**
     * Invalid request
     *
     *
     */
    public function invalidAction() {
        $this->getResponse()->setHttpResponseCode(403);
    }
}