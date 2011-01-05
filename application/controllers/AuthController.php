<?php

/**
 * AuthController
 *
 * user login/logout
 *
 * @category Application
 * @todo refactor move to user module
 */
class AuthController extends Zend_Controller_Action
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
        $form = new App_Form_Auth_Login;
        new Patchwork_Form_Tableize($form);

        $request = $this->getRequest();

        if ($request->isPost()) {
            $params = $request->getParams();
            if ($form->isValid($params)) {
                $identity = $request->getParam(User::AUTH_IDENTITY_COLUMN);
                $credential = $request->getParam(User::AUTH_CREDENTIAL_COLUMN);

                if (User::authenticate($identity, $credential)) {
                    return $this->_forward('welcome');
                }

                $this->view->messages = User::getAuthenticationResult()
                        ->getMessages();
            } else {
                $form->populate($params);
            }
        }

        $this->view->form = $form;
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
