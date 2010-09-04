<?php
/**
 * Patchwork
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage Plugin
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * REST API plugin
 *
 * enables rest routing and http auth
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage Plugin
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_Controller_Plugin_RESTAPI extends Zend_Controller_Plugin_Abstract
{
    /**
     * default module which is used for rest routing: api
     * @var string
     */
    const DEFAULT_API_MODULE_NAME = 'api';

    /**
     * enables rest routing for the configured module and enables http auth plugin
     *
     * use in application.ini:
     * patchwork.options.restAPI.module = 'api'
     * patchwork.options.restAPI.useHttpBasicAuth = true
     * 
     * @param Zend_Controller_Request_Abstract $request
     * FIXME overwrites all routing with restRoute
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $restRoutingModule = self::DEFAULT_API_MODULE_NAME;
        $httpBasicAuth = false;
        if(Zend_Registry::isRegistered(Patchwork::CONFIG_REGISTRY_KEY)){
            $config = Zend_Registry::get(Patchwork::CONFIG_REGISTRY_KEY);
            if($config->patchwork && $config->patchwork->restAPI){
                $restRoutingModule = $config->patchwork->restAPI->module;
                $httpBasicAuth = $config->patchwork->restAPI->useHttpBasicAuth;
            }
        }

        $frontController = Zend_Controller_Front::getInstance();
        $restRoute = new Zend_Rest_Route(
            $frontController,
            array(),
            array($restRoutingModule)
        );
        $frontController->getRouter()->addRoute('rest', $restRoute);

        if($httpBasicAuth){
            $plugin = new Patchwork_Controller_Plugin_HttpAuth($restRoutingModule);
            $frontController->registerPlugin($plugin);
        }
        parent::routeStartup($request);
    }
}