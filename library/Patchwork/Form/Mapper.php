<?php
/**
 * Patchwork_Form_Mapper
 * 
 * A mapper  to create/modify models from form data and vice versa
 *
 * <code>
 * $mapper = new XForm_Mapper();
 * $mapper->populateFormWith($model);
 * $this->view->form = $mapper->getForm();
 * $mapper->applyFormDataTo($model);
 * </code>
 *
 * @package    Patchwork
 * @subpackage Form
 * @category   Library
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
interface Patchwork_Form_Mapper
{
    /**
     * populate the form
     *
     * @param Patchwork_Form_Model $mode a model delivering the data
     */
    function populateFormWith(Patchwork_Form_Model $model);

    /**
     * set data to model
     * @return boolean
     */
    function applyFormDataTo(Patchwork_Form_Model $model);

    /**
     * pass the form instance
     *
     * @param Zend_Form $form form instance
     * @return Patchwork_Form_Mapper
     */
    function setForm(Zend_Form $form);

    /**
     * @return Zend_Form
     */
    function getForm();
}