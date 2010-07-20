<?php
/**
 * takes a Zend_Form and sets all decorators to render a table-based form
 *
 * @author     Daniel Pozzi
 * @category   Library
 * @package    Patchwork
 * @subpackage Form
 */

/**
 * takes a Zend_Form and sets all decorators to render a table-based form
<code>
new Patchwork_Form_Tableize($myForm);
</code>
 * @author     Daniel Pozzi
 * @category   Library
 * @package    Patchwork
 * @subpackage Form
 */
class Patchwork_Form_Tableize
{
    /**
     * element decorators
     * @var array
     */
    public $elementDecorators = array(
        'ViewHelper',
        'Errors',
        array(
            array('data' => 'HtmlTag'),
            array('tag' => 'td', 'class' => 'element')
        ),
        array('Label', array('tag' => 'td')),
        array(array('row' => 'HtmlTag'),
            array('tag' => 'tr')
        ),
    );

    /**
     * button decorators
     * @var array
     */
    public $buttonDecorators = array(
        'ViewHelper',
        array(array('data' => 'HtmlTag'),
        array('tag' => 'td', 'class' => 'element')),
        array(array('label' => 'HtmlTag'),array('tag' => 'td', 'placement' => 'prepend')),
        array(array('row' => 'HtmlTag'),
        array('tag' => 'tr')),
    );

    /**
     * constructor takes form as argument
     *
     * @param Zend_Form $form zend form instance
     *
     * @return self
     */
    public function __construct(Zend_Form $form = null)
    {
       $this->tableize($form);
    }

    /**
     * apply decorators to all elements
     *
     * @param Zend_Form $form form
     */
    public function tableize(Zend_Form $form)
    {
        $form->setDecorators(
            array(
                'FormElements',
                array('HtmlTag', array('tag' => 'table')),
                'Form',
            )
        );

        foreach($form->getElements() as $el){
            if($this->_isButton($el))
                $el->clearDecorators()->setDecorators($this->buttonDecorators);
            else
                $el->clearDecorators()->setDecorators($this->elementDecorators);
        }

        foreach($form->getSubForms() as $sf)
            $this->tableize($sf);
    }

    /**
     * check ig button decorators are needed
     *
     * @param Zend_Form_Element $el
     * 
     * @return boolean
     * @todo captchas, else?
     */
    protected function _isButton(Zend_Form_Element $el)
    {
        return $el instanceof Zend_Form_Element_Submit ||
            $el instanceof Zend_Form_Element_Button;
    }
}