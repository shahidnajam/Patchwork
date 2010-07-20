<?php
/**
 * Simple login form
 *
 * @category Application
 * @package  Authentication
 * @subpackage Forms
 * @author Daniel Pozzi
 */
class App_Form_Auth_Login extends Zend_Form
{
    /**
     * just email + credential field
     * 
     */
    public function init()
    {
        $this->addElement(
            $this->createElement('text', User::AUTH_IDENTITY_COLUMN)
            ->setRequired(true)
            ->setLabel(User::AUTH_IDENTITY_COLUMN)
        );

        $this->addElement(
            $this->createElement('password', User::AUTH_CREDENTIAL_COLUMN)
            ->setRequired(true)
            ->setLabel(User::AUTH_CREDENTIAL_COLUMN)
        );

        $this->addElement($this->createElement('submit', 'Login'));
    }
}