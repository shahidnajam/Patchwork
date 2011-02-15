<?php

/**
 * AuthController
 *
 * user login/logout
 *
 * @category Application
 * @todo refactor move to user module
 */
class User_AuthController extends Patchwork_Controller_Action
{

    /**
     *
     * @var Zend_Auth_Result
     */
    public $result;

    /*
     *
     *
     *
     */

    public function indexAction()
    {
        if (Zend_Auth::getInstance()->hasIdentity())
            $this->_redirect('user/view');
        else
            $this->_forward('login');
    }

    /*
     * perform login
     *
     *
     */

    public function loginAction()
    {
        $this->view->form = $this->getContainer()->getFormFactory()
            ->getGenericFormFromIni(
                APPLICATION_PATH . '/modules/user/forms/login.ini',
                'login'
            );
    }

    /*
     *
     *
     *
     */

    public function logoutAction()
    {
        Zend_Registry::get('session')->unsetAll();
        $result = Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy(true);

        $this->_redirect('user/loggedout');
    }

    /**
     *
     */
    public function welcomeAction()
    {
        
    }

}
