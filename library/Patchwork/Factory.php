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
        $iter = new DirectoryIterator($path);
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
}