<?php
/**
 * Patchwork
 *
 * @category    Application
 * @package     Default
 * @subpackage  Bootstrap
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */
/**
 * Bootstrap class
 *
 * registers registry keys and starts Doctrine
 *
 * @package    Application
 * @subpackage Default
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
class Bootstrap extends Patchwork_Bootstrap
{

    /**
     * init app-wide locale
     *
     * @return Zend_Locale
     */
    public function _initLocale()
    {
        $locale = new Zend_Locale();
        Zend_Registry::set('Zend_Locale', $locale);

        // Return it, so that it can be stored in the container
        return $locale;
    }

    /**
     * command line interface bootstrapping
     *
     * 
     */
    public function _initCli()
    {
        $this->_initAppAutoload();
    }

}
