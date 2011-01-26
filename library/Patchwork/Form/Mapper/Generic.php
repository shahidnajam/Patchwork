<?php
/**
 * Patchwork_Form_Mapper_Generic
 *
 * A generic implementation of a form mapper. Provides checking for hash
 * element
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage Form
 * @author     Daniel Pozzi
 *
 * <code>
 * /** in a controller action *\/
 *
 * $mapper = new Patchwork_Form_Mapper_Generic;
 * $mapper->setForm($form);
 * $mapper->populateFormWith($myModel);
 * $this->view->form = $mapper->getForm();
 *
 * if($this->getRequest()->isPost())
 * {
 *     $mapper->applyFormDataTo($myModel);
 * }
 * </code>
 */
class Patchwork_Form_Mapper_Generic implements Patchwork_Form_Mapper
{
    /**
     * flag whether to check the form for a hash element
     * 
     * @var boolean
     */
    private $checkForHash = true;

    /**
     * form
     * @var Zend_Form
     */
    private $form;

    /**
     * populate the form
     * 
     * @param Patchwork_Form_Model $model model
     */
    public function populateFormWith(Patchwork_Form_Model $model)
    {
        $this->form->populate($model->getDataForForm());
    }

    /**
     * apply data to model
     * 
     * @param Patchwork_Form_Model $model model instance
     */
    public function applyFormDataTo(Patchwork_Form_Model $model)
    {
        if ($this->form->isValid($data = $this->form->getValues())) {
            $model->setDataFromForm($data);
        }
    }

    /**
     * set form
     * 
     * @param Zend_Form $form form
     * @return Patchwork_Form_Mapper
     * @throws Patchwork_Exception
     */
    public function setForm(Zend_Form $form)
    {
        if ($this->checkForHash) {
            $hashFound = false;
            foreach ($form->getElements as $element) {
                if($element instanceof Zend_Form_Element_Hash) {
                    $hashFound = true;
                    break;
                }
            }
            if (!$hashFound) {
                throw new Patchwork_Exception('Form contains no hash element.');
            }
        }
        
        $this->form = $form;
        return $this;
    }
    
    /**
     * get the form
     * 
     * @return Zend_Form
     */
    public function  getForm()
    {
        return $this->form;
    }
}