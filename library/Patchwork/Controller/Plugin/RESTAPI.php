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
 * enables rest routing and http auth for a specific module
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage Plugin
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_Controller_Plugin_RESTAPI extends Zend_Controller_Plugin_Abstract
{

    /**
     * enables rest routing for the configured module and enables http auth plugin
     *
     * use in application.ini:
     * patchwork.options.restAPI.module = 'api'
     * patchwork.options.restAPI.useHttpBasicAuth = true
     * 
     * @param Zend_Controller_Request_Abstract $request request object
     * @return void
     * @throws Patchwork_Exception
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $httpBasicAuth = false;
        $container = Patchwork_Container::getBootstrapContainer();
        if ($config = $container->config) {
            if ($config->patchwork && $config->patchwork->options->restAPI) {
                $restRoutingModule = $config->patchwork->options->restAPI->module;
                $httpBasicAuth = $config->patchwork->options->restAPI->useHttpBasicAuth;
            } else {
                throw new Patchwork_Exception('REST API configuration missing');
            }
        }

        $frontController = Zend_Controller_Front::getInstance();
        $restRoute = new Zend_Rest_Route(
                $frontController,
                array(),
                array($restRoutingModule)
        );
        $frontController->getRouter()->addRoute('rest', $restRoute);

        if ($httpBasicAuth) {
            $plugin = $container->getInstance(
                'Patchwork_Controller_Plugin_Auth_HttpBasic'
            );
            $frontController->registerPlugin($plugin);
        }
        parent::routeStartup($request);
    }

}