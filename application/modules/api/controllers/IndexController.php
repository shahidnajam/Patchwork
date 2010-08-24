<?php
/**
 * 
 * 
 * 
 */
class Api_IndexController extends Patchwork_Controller_RESTModelController
{

    /**
     * 
     */
    public function indexAction()
    {
        $p = Zend_Controller_Front::getInstance()->getPlugin(
            'Patchwork_Controller_Plugin_HttpAuth'
        );
        if($p)
            echo "HttpAuth plugin loaded.";
    }

    /**
     * access denied
     * 
     */
    public function  deniedAction()
    {
        $this->getResponse()->setHttpResponseCode(403);
        $this->getResponse()->setBody('Access denied');
    }
}