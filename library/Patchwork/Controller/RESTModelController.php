<?php
/**
 * REST Controller plus Doctrine, uses basic http auth
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
     * @var Doctrine_Record
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
        try{
            $this->_helper->layout->disableLayout();
        } catch (Zend_Controller_Action_Exception $e){
            //layout helper was not present
        }

        try{
        $this->_helper->viewRenderer->setNoRender(true);
        } catch (Zend_Controller_Action_Exception $e){
            //ok, since not needed
        }
        
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
     * returns the get url for a model: /users/1
     *
     * @param Doctrine_Record $model
     * 
     * @return string
     */
    public function getModelURL(Doctrine_Record $model)
    {
        $url = $_SERVER['HTTP_HOST']
            . Zend_Controller_Front::getInstance()->getBaseUrl()
            . DIRECTORY_SEPARATOR . $this->getRequest()->getControllerName()
            . DIRECTORY_SEPARATOR . $model->{$this->modelPrimaryKey};

        return $url;
    }

    /**
     * returns limit param
     * @return int
     */
    protected function _getLimit()
    {
        return (int)$this->_getParam(self::LIMIT_PARAM, 0);
    }

    /**
     * returns offset param
     * @return int
     */
    protected function _getOffset()
    {
        return (int)$this->_getParam(self::OFFSET_PARAM, 0);
    }

    /**
     * list, the standard GET request without an id
     *
     * response is a json array with fields: limit, offset, total, data
     */
    public function indexAction()
    {
        $objects = $this->_helper->Doctrine->listRecords(
            $this->modelName,
            $where = null,
            $this->_getLimit(),
            $this->_getOffset()
        );

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json')
            ->appendBody($this->_helper->Doctrine->toJSON($objects));
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
                $this->_helper->Doctrine->toJSON($this->model)
            );
    }

    /**
     * put (update)
     *
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

        $this->model->fromArray($params);
        $this->model->save();
        
        $url = $this->getModelURL($this->model);

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader("Location", $url)
            ->appendBody($this->_helper->Doctrine->toJSON($this->model));
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
        try {
            $model->save();
        } catch (Exception $e) {
            return $this->errorAction('Error creating entity.');
        }
        
        if($model->id) {
            $this->getResponse()
                ->setHttpResponseCode(201)
                ->setHeader("Location", $this->getModelURL($model))
                ->appendBody($this->_helper->Doctrine->toJSON($model));
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

        if(!$this->model->delete())
            return $this->errorAction();
        
        $this->getResponse()
            ->setHttpResponseCode(204);
    }

    /**
     * error sends code 500
     *
     * @param string $message error message
     */
    public function errorAction($message = null)
    {
        $this->getResponse()->setHttpResponseCode(500);
        if($message)
            $this->getResponse()->setBody($message);
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