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
 * Patchwork_Form_Redecorator_Table
 *
 * sets the decorators to render the form as a table
 *
 * @category    Library
 * @package     Forms
 * @subpackage  Redecorators
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_Form_Redecorator_Table implements Patchwork_Form_Redecorator
{
    /**
     * options
     * @var array
     */
    public $options = array();

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
     * Constructor
     */
    public function  __construct(array $options = array())
    {
        $this->options['elementDecorators'] = $this->elementDecorators;
        $this->options['buttonDecorators']  = $this->buttnDecorators;

        array_merge($this->options, $options);
    }

    /**
     * redecorate
     * 
     * @param Zend_Form $form form to redecorate
     * 
     * @return Patchwork_Form_Redecorator_Table
     */
    public function redecorate(Zend_Form $form)
    {
        $form->setDecorators(
            array(
                'FormElements',
                array('HtmlTag', array('tag' => 'table')),
                'Form',
            )
        );

        foreach($form->getElements() as $el){
            $el->clearDecorators();
            if($this->_isButton($el)) {
                $el->setDecorators($this->buttonDecorators);
            } else {
                $el->setDecorators($this->elementDecorators);
            }
        }

        foreach($form->getSubForms() as $sf) {
            $this->redecorate($sf);
        }

        return $this;
    }

    /**
     * check if button decorators are needed
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