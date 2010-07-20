<?php
/*
 * AuthController
 *
 * user login/logout
 *
 * @category Application
 *
 */
class AuthController extends Zend_Controller_Action
{
	protected $userModelName = 'M2_Model_User';
	protected $userModel;

	/*
	 * which model ist stored in session as identity?
	 */
	protected $storageModelName = 'M2_Model_User';

	/*
	 * this comes from the login form
	 */
	protected $requestIdentity = 'username';
	protected $requestCredential = 'password';

	/*
	 *
	 *
	 *
	 */
	public function indexAction()
	{
		if(Zend_Auth::getInstance()->hasIdentity())
			$this->_redirect('user/view');
		else
			$this->_forward('login');
	}

	/**
     * login
     *
     * @param string $identity
     * @param string $credential
     *
     * @return boolean
     */
	protected function _login(
        $identity,
        $credential
    ){
        $adapter = new Patchwork_Auth_Adapter_Doctrine(
            Doctrine::getTable('User')->getTableName(),
            User::AUTH_IDENTITY_COLUMN,
            User::AUTH_CREDENTIAL_COLUMN,
            User::AUTH_CREDENTIAL_TREATMENT
            );
		$adapter->setIdentity($identity);
		$adapter->setCredential($credential);

		$result = Zend_Auth::getInstance()->authenticate($adapter);

		/*
		 * save object on success
		 */
		if($result->isValid() && Zend_Auth::getInstance()->hasIdentity())
		{
			$id = Zend_Auth::getInstance()->getIdentity();
            $user = Doctrine::getTable('User')
                ->findWhere(User::AUTH_IDENTITY_COLUMN, $id);

			Zend_Auth::getInstance()->getStorage()->write($user);
			return true;
		}
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
        
		if($request->isPost()){
            $params = $request->getParams();
            if($form->isValid($params)){
                $identity = $request->getParam( User::AUTH_IDENTITY_COLUMN );
                $credential = $request->getParam( User::AUTH_CREDENTIAL_COLUMN );

                if($this->_login($identity, $credential)){
                    $this->_forward('welcome');
                }

                $this->view->error = 'login_failed';
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
