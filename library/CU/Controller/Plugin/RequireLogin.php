<?php
/**
 * The simplest auth plugin. Just requires an identity in auth,
 * that's all.
 */
class CU_Controller_Plugin_RequireLogin extends Zend_Controller_Plugin_Abstract
{
	public function preDispatch(Zend_Controller_Request_Abstract $request)
	{
		if(!Zend_Auth::getInstance()->hasIdentity())
		{
			$request->setModuleName('auth')
			        ->setControllerName('auth')
                    ->setActionName('login');
		}
	}
}
