<?php
/**
 * Patchwork
 *
 * @category    Library
 * @package     Patchwork
 * @subpackage  Controller
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * REST Controller, uses basic http auth
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
     * request constants
     */
    const OFFSET_PARAM = 'offset';
    const LIMIT_PARAM  = 'limit';
    const ORDER_PARAM  = 'orderby';

    /**
     * get the dependency injection container
     *
     * @return Patchwork_Container
     */
    public function getContainer()
    {
        return $this->_invokeArgs['bootstrap']->getContainer();
    }

    /**
     * get the storage service instance
     * 
     * @return Patchwork_Storage_Service
     */
    public function getStorageService()
    {
        return $this->getContainer()->getInstance('Patchwork_Storage_Service');
    }

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
    }

    /**
     * returns the get url for a model: /users/1
     *
     * @param Doctrine_Record $model
     * 
     * @return string
     */
    protected function getModelURL($model)
    {
        if(!is_object($model)) {
            throw new InvalidArgumentException('Object required');
        }

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
    protected function  _getLimit()
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

    protected function _getOrderBy()
    {
        return $this->_getParam(self::ORDER_PARAM, null);
    }

    /**
     * retrieves a storage record by configured name and pk
     * 
     * @return object
     */
    protected function findObject()
    {
        $object = $this->getStorageService()->find(
            $this->modelName,
            (int)$this->_getParam($this->modelPrimaryKey)
        );

        return $object;
    }

    /**
     * list, the standard GET request without an id
     *
     * response is a json array with fields: limit, offset, total, data
     */
    public function indexAction()
    {
        $objects = $this->getStorageService()->fetch(
            $this->modelName,
            $where = null,
            $this->_getOrderBy(),
            $this->_getLimit(),
            $this->_getOffset()
        );

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json')
            ->appendBody(new Patchwork_Decorator_JSON($objects));
    }

    /**
     * GET: find one object by primary key
     *
     *
     */
    public function getAction()
    {
        if (!$object = $this->findObject()) {
            return $this->setNotFoundCode();
        }

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Content-type', 'application/json')
            ->appendBody(new Patchwork_Decorator_JSON($object));
    }

    /**
     * put (update)
     *
     */
    public function putAction()
    {
        if (!$object = $this->findObject()) {
            return $this->setNotFoundCode();
        }

        $this->getStorageService()->setObjectValues($object, $this->_getAllParams());
        $this->getStorageService()->save($object);
        
        $url = $this->getModelURL($object);

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader("Location", $url)
            ->appendBody(new Patchwork_Decorator_JSON($object));
    }

    /**
     * post (create), sends back the url
     *
     *
     */
    public function postAction()
    {
        $object = new $this->modelName;
        $this->getStorageService()->setObjectValues(
            $object,
            $this->_getAllParams()
        );

        if(!$this->getStorageService()->save($object)) {
            return $this->setErrorCode(
                'Could not create ' . $this->modelName . ': '.
                $this->getStorageService()->getErrorMessage()
            );
        }
        
        $this->getResponse()
            ->setHttpResponseCode(201)
            ->setHeader("Location", $this->getModelURL($object))
            ->appendBody(new Patchwork_Decorator_JSON($object));
    }

    /**
     * delete
     *
     *
     */
    public function deleteAction()
    {
        if (!$object = $this->findObject()) {
            return $this->_returnNotfound();
        }

        if (!$this->getStorageService()->delete($object)) {
            return $this->setErrorCode('Delete not possible');
        }
        
        $this->getResponse()->setHttpResponseCode(204);
    }

    /**
     * error sends code 500
     *
     * @param string $message error message
     */
    protected function setErrorCode($message = null)
    {
        $this->getResponse()->setHttpResponseCode(500);
        if ($message) {
            $this->getResponse()->setBody($message);
        }
    }

    /**
     * Invalid request
     */
    protected function setInvalidActionCode()
    {
        $this->getResponse()->setHttpResponseCode(403);
    }

    /**
     * not found
     */
    protected function setNotFoundCode()
    {
        $this->getResponse()->setHttpResponseCode(404);
    }
}