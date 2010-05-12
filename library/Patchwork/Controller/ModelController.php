<?php
/**
 * Patchwork_Controller_ModelController
 *
 * a controller that is bound to a specific model, used to CRUD
 *
 * @package Patchwork
 * @subpackage Controller
 * @author Daniel Pozzi
 *
 */
abstract class Patchwork_Controller_ModelController
    extends Zend_Controller_Action
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
     * finds the model if PK is in request
     *
     * @return Doctrine_Record
     */
    public function initModel($withPrimaryKey = true)
    {
        if(!$withPrimaryKey)
            return new $this->modelName;
        
        return $this->model = $this->Doctrine
                                ->getRecordOrException(
                                    $this->modelName,
                                    $this->_getParam($this->modelPrimaryKey)
                                );
    }

    /**
     * default: list all
     *
     *
     */
    public function indexAction() {
        $this->view->objects = Doctrine::getTable($this->modelName)->findAll();
    }

    /**
     * view particular
     *
     */
    public function viewAction() {
        $this->_initModelByPrimaryKey();
        if($this->model)
            $this->view->object = $this->model;
    }

    /**
     * create
     *
     *
     */
    public function createAction() {
        $this->view->form = Patchwork_Doctrine_ModelForm::factory(
            $this->modelName
        );
    }

    /**
     * edit
     *
     *
     */
    public function editAction() {
        $this->_initModelByPrimaryKey();
        $this->view->form = Patchwork_Doctrine_ModelForm::factory(
            $this->model
        );
    }

    /**
     * delete
     *
     *
     */
    public function deleteAction() {
        $this->_initModelByPrimaryKey();
    }
}