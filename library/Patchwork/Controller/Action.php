<?php
/**
 * Patchwork
 *
 * @category    Library
 * @package     Patchwork
 * @subpackage  Controller
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * Patchwork_Controller_Action
 *
 * Instead of inheriting from Patchwork_Controller_Action you can 
 * 1) implement getContainer in your controller or
 * 2) use a controller helper that calls Patchwork_Container::getBootstrapContainer()
 *
 * @category    Library
 * @package     Patchwork
 * @subpackage  Controller
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_Controller_Action extends Zend_Controller_Action
{
    /**
     * get the dependency injection container
     * 
     * @return Patchwork_Container
     */
    protected function getContainer()
    {
        return $this->_invokeArgs['bootstrap']->getContainer();
    }
}