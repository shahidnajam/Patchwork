<?php
/**
 * Patchwork
 *
 * @category    Library
 * @package     Forms
 * @subpackage  Redecorators
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * Redecorator Interface
 *
 * @category    Library
 * @package     Forms
 * @subpackage  Redecorators
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */
interface Patchwork_Form_Redecorator
{
    /**
     * redecorate a form
     * 
     * @param Zend_Form $form form
     */
    function redecorate(Zend_Form $form);
}