<?php
/**
 * REST Controller plus Doctrine
 *
 * @package Patchwork
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
        $this->_helper->viewRenderer->setNoRender(true);
    }
    
    /**
     * finds the model if PK is in request
     *
     * @return Doctrine_Record
     */
    public function initModel($withPrimaryKey = true)
    {
        if(!$withPrimaryKey)
            return new $this->modelName;

        return $this->model = $this->_helper->Doctrine
            ->getRecordOrException(
            $this->modelName,
            $this->_getParam($this->modelPrimaryKey)
        );
    }

    /**
     * list
     */
    public function indexAction()
    {
        $this->_forward('get');
    }

    /**
     * listing
     *
     *
     *
     */
    public function getAction()
    {
        $offset = $this->_getParam(self::OFFSET_PARAM);
        $objects = $this->Doctrine->list($this->modelName);

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->appendBody(
            Zend_Json::encode($objects->toArray())
        );
    }

    /**
     * put (update)
     *
     * @todo check resp code
     */
    public function putAction() {
        try {
            $model = $this->initModel();
        } catch (Exception $e) {
            $this->_forward('invalid');
        }

        $model->fromArray($this->getRequest()->getParams());
        $url = Zend_Controller_Front::getInstance()->getBaseUrl() . DS
            . $this->modelName . DS . $model->id;

        $this->getResponse()
            ->setHttpResponseCode(201)
            ->setHeader("Location", $url)
            ->appendBody($url);
    }

    /**
     * post (create), sends back the url
     *
     *
     */
    public function postAction()
    {
        $model = $this->initModel(false);
        $model->fromArray($this->getRequest()->getParams());
        $model->save();

        if($model->id) {
            $url = Zend_Controller_Front::getInstance()->getBaseUrl() . DS
                . $this->modelName . DS . $model->id;

            $this->getResponse()
                ->setHttpResponseCode(201)
                ->setHeader("Location", $url)
                ->appendBody($url);
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