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
     * @var string
     * @todo make configurable
     */
    const API_MODULE_NAME = 'api';

    /**
     * enables rest routing for the API module
     * and http auth plugin
     * 
     * @param Zend_Controller_Request_Abstract $request
     * FIXME overwrites all routing with restRoute
     */
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $frontController = Zend_Controller_Front::getInstance();
        $restRoute = new Zend_Rest_Route($frontController);
        $frontController->getRouter()->addRoute(self::API_MODULE_NAME, $restRoute);

        $plugin = new Patchwork_Controller_Plugin_HttpAuth(self::API_MODULE_NAME);
        $frontController->registerPlugin($plugin);
        parent::routeStartup($request);
    }
}