<?php
/**
 * Patchwork
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage View
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * Patchwork_View
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage View
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 * @copyright  Rob Allen
 * @link       http://akrabat.com/wp-content/uploads/PHPUK11-Optimising_ZF1.pdf
 */
class Patchwork_View extends Zend_View
{

    /**
     * url view helper
     * 
     * @param array  $urlOptions
     * @param string $name
     * @param bool   $reset
     * @param bool   $encode
     * @return string
     */
    public function url($urlOptions = array(), $name = null, $reset = false, $encode = true)
    {
        $router = Zend_Controller_Front::getInstance()->getRouter();
        return $router->assemble(
            $urlOptions,
            $name,
            $reset,
            $encode
        );
    }

    /**
     * escape
     * 
     * @param string $var string to escape
     * 
     * @return string
     */
    public function escape($var)
    {
        return htmlspecialchars($var, ENT_COMPAT, $this->_encoding);
    }

}