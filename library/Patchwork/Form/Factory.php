<?php
/**
 * Patchwork
 *
 * @category    Library
 * @package     Forms
 * @subpackage  Factory
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * Patchwork_Form_Factory
 *
 * allows to add form functionality by composition
 *
 * @category    Library
 * @package     Forms
 * @subpackage  Factory
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_Form_Factory
{
    /**
     * add a csrf protection hash to a form
     *
     * @param Zend_Form $form form to add hash to
     * @param string    $name name of the element, default is csrf_token
     *
     * @return Patchwork_Form_Factory
     */
    public function addHash(Zend_Form $form, $name = 'csrf_token')
    {
        $form->addElement('hash', $name);
        return $this;
    }

    /**
     * add element from an ini file
     *
     * @param Zend_Form $form     form
     * @param string    $filename file location
     * @param string    $section  section to load
     * @param array     $options  options
     *
     * @return Patchwork_Form_Factory
     */
    public function addElementsFromIni(
        Zend_Form $form,
        $filename,
        $section = null,
        $options = null
    ) {
        $form->setConfig(new Zend_Config_Ini($filename, $section, $options));
        return $this;
    }

    /**
     * replace all decorators of a form (apply after all element were added)
     *
     * @param Zend_Form $form    form to redecorate
     * @param string    $style   name of the redecorator
     * @param array     $options redecorator options
     *
     * @return Patchwork_Form_Factory
     */
    public function redecorate(
        Zend_Form $form,
        $style = 'table',
        array $options = array()
    ) {
        $redecorator = $this->_getRedecorator($style, $options);
        $redecorator->redecorate($form);

        return $this;
    }

    /**
     * get a redecorator
     * 
     * @param string $name    name of the redecorator
     * @param array  $options constructor options
     *
     * @return Patchwork_Form_Redecorator
     */
    private function _getRedecorator($name, $options)
    {
        $className = 'Patchwork_Form_Redecorator_' . ucfirst(strtolower($name));

        return new $className($options);
    }
}