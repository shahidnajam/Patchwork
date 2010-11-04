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
    public function getContainer()
    {
        return $this->_invokeArgs['bootstrap']->getContainer();
    }

}