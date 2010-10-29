<?php
/**
 * Example Index Controller, associated to User model
 *
 * 
 */
class IndexController extends Patchwork_Controller_Action
{
 
    /**
     * index, just to test the ModelController
     *
     *
     */
    public function indexAction(){
        $doctrine = $this->_getDoctrine();
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
        $this->view->form = new Patchwork_Doctrine_Model_Form($this->model);
        new Patchwork_Form_Tableize($this->view->form);
    }

    /**
     * 
     */
    public function helpersAction()
    {
        $this->view->model = $this->_helper->Doctrine('User',1);
    }

    /**
     * 
     */
    public function testcacheAction()
    {
        $query = Doctrine_Query::create()->from('User')
            ->where('id > ?', 0)
            ->andWhere('username = ?', 'alex');
        $res = $query->execute();

        var_dump($res->toArray());
        exit();
    }
}
