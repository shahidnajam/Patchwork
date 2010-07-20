<?php
/**
 * Example Index Controller, associated to User model
 *
 * 
 */
class IndexController extends Zend_Controller_Action
{
 
    /**
     * index, just to test the ModelController
     *
     *
     */
    public function indexAction(){
        $this->_helper->getHelper('Doctrine');
        $this->model = new User;
        $this->model->email = 'test@test.com';
        $this->view->user = $this->model;
    }

    /**
     *
     */
    public function badrequestAction()
    {
       if(!isset($_GET['test'])){
           $this->_redirect('/index/badrequest/?test=<script>');
       }
    }

    /**
     * 
     */
    public function formAction()
    {
        $this->model = new User;
        $this->model->email = 'test@test.com';
        $this->view->form = new Patchwork_Form_Doctrine_ModelForm($this->model);
        new Patchwork_Form_Tableize($this->view->form);
    }

    /**
     * 
     */
    public function helpersAction()
    {
        $this->view->model = $this->_helper->Doctrine('User',1);
    }
}
