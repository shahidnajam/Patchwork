<?php
/**
 * Patchwork
 *
 * @category    Library
 * @package     Patchwork
 * @subpackage  Factory
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * Patchwork_Factory
 *
 * Factory for Acl
 *
 * @category    Library
 * @package     Patchwork
 * @subpackage  Factory
 * @author      Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_Factory
{
    /**
     * factory method
     *
     * @param Zend_Controller_Request_Abstract $request request
     *
     * @return Zend_Acl
     */
    public static function acl()
    {
        $staticPath = '/configs/acl.ini';
        $acl = new Zend_Acl();
        $defaultIni = APPLICATION_PATH . $staticPath;
        if(is_file($defaultIni)) {
            $config = new Patchwork_Acl_Ini($defaultIni);
            $config->addConfigToAcl($acl);
        }

        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules';
        try {
            $iter = new DirectoryIterator($path);
        } catch (UnexpectedValueException $e) {
            /** directory is not present */
            return $acl;
        }
        foreach ($iter as $moduleDir) {

            if (!$moduleDir->isDir() || $moduleDir->isDot()) {
                 continue;
            }
            $iniFile = APPLICATION_PATH . '/modules/' . $moduleDir . $staticPath;
            if(!is_file($iniFile)){
                continue;
            }

            $config = new Patchwork_Acl_Ini($iniFile);
            $config->addConfigToAcl($acl);
        }

        return $acl;
    }

    /**
     *
     * @return Zend_Controller_Request_Abstract
     */
    public function request()
    {
        return Zend_Controller_Front::getInstance()->getRequest();
    }

    /**
     * create a Zend_Navigation
     * 
     * @return Zend_Navigation
     */
    public function navigation($path = null)
    {
        if($path == null) {
            $path = CONFIG_PATH . DIRECTORY_SEPARATOR . 'navigation.php';
        }

        return new Zend_Navigation(require($path));
    }
}