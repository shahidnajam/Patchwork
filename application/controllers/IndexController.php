<?php
/**
 * Patchwork
 *
 * @category    Application
 * @package     Default
 * @subpackage  Controllers
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * Patchwork
 *
 * @category    Application
 * @package     Default
 * @subpackage  Controllers
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */
class IndexController extends Patchwork_Controller_Action
{
 
    /**
     * index, just to test the ModelController
     *
     *
     */
    public function indexAction(){
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
