<?php
/**
 * Integration test
 *
 * @category Testing
 * @package  Patchwork
 * @subpackage Model Form
 */
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/bootstrap.php';

/**
 * test the model form
 *
 * @category Testing
 */
class Patchwork_Doctrine_Model_FormTest extends ControllerTestCase
{
    /**
     * integration test with user model
     */
    public function testFormDisplaysForUserModel()
    {
        $form = new Patchwork_Doctrine_Model_Form(new User);

        $this->assertTrue($form instanceof Patchwork_Doctrine_Model_Form);
        $this->assertTrue($form->getElement('save') instanceof Zend_Form_Element_Submit);
        $this->assertTrue(
            $form->getElement(Patchwork_Doctrine_Model_Form::CSRF_PROTECTION_FIELD)
            instanceof Zend_Form_Element_Hash
        );

        $username = $form->getElement('username');
        $this->assertTrue($username instanceof Zend_Form_Element_Text);

        $id = $form->getElement('id');
        $this->assertTrue($id instanceof Zend_Form_Element, serialize($id));
    }
}