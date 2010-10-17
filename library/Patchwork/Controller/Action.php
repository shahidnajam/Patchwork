<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Patchwork_Controller_Action extends Zend_Controller_Action
{
    /**
     * @return Zend_Application_Bootstrap_Bootstrap
     */
    public function getBootstrap()
    {
        return $this->_invokeArgs['bootstrap'];
    }

    /**
     * get the doctrine default connection
     * 
     * @return Doctrine_Connection|null
     */
    protected function _getDoctrine()
    {
        return $this->getBootstrap()->getResource('doctrine');
    }

    /**
     * get the bootstrap config
     * 
     * @return Zend_Config
     */
    protected function _getConfig()
    {
        return new Zend_Config($this->getBootstrap()->getOptions());
    }
}