<?php
/**
 * PHPIDS plugin
 *
 * requires the application config to be registered in registry as "config", and
 * that config includes
 *
 * @package Patchwork
 * @subpackage Security
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * PHPIDS plugin
 *
 * - requires the application config to be registered in registry as "config",
 * - and that config includes a "phpids" section
 * - IDS to be autoloadable (e.g. in library)
 *
 * @package Patchwork
 * @subpackage Security
 * @author Daniel Pozzi <bonndan76@googlemail.com>
 */
class Patchwork_Controller_Plugin_PHPIDS extends Zend_Controller_Plugin_Abstract
{
    /**
     * where to find the phpids settings in the config
     */
    const PHPIDS_CONFIG_SECTION = 'phpids';
    
    /**
     * configuration
     * @var Zend_Config
     */
	private $config = null;

    /**
     * constructors read config
     *
     * @return App_Controller_Plugin_PHPIDS
     */
	public function __construct()
	{
		$this->config = Zend_Registry::get(Patchwork::CONFIG_REGISTRY_KEY)
            ->{self::PHPIDS_CONFIG_SECTION};
        
	}

    /**
     * scans the request
     *
     * @param Zend_Controller_Request_Abstract $request
     */
	public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
	    $input = array(
	        'REQUEST' => $_REQUEST,
	        'GET' => $_GET,
	        'POST' => $_POST,
	        'COOKIE' => $_COOKIE
	    );
	    $init = IDS_Init::init();
	    $init->config = $this->config->toArray();
        
	    // run the ids monitor
	    $ids = new IDS_Monitor($input, $init);
	    $result = $ids->run();
	    // analyze the result
	    if (!$result->isEmpty()) {
	    	$impact = $result->getImpact();
	        // log the event
	        $compositeLog = new IDS_Log_Composite();
	        $compositeLog->addLogger(IDS_Log_File::getInstance($init));
	        $compositeLog->execute($result);
	        // for high impact, we redirect to error page
	        if($impact >= $this->config->impact_threshold) {
	        	$request->setControllerName('error');
                $request->setActionName('badrequest');
	        }
	    }
    }
}
