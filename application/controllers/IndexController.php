<?php
/**
 * Index Controller, associated to User model
 *
 * 
 */
class IndexController extends Patchwork_Controller_ModelController
{
    /**
     * Doctrine User model
     * @var string
     */
    var $modelName = 'User';
    
    /**
     * index
     *
     *
     */
    public function indexAction(){
        $this->model = new User;
        $this->view->user = $this->model;

        $this->view->form = Patchwork_Doctrine_ModelForm::with($this->model);
    }
}
