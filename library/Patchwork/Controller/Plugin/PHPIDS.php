<?php

/**
 * Patchwork
 *
 * @category   Library
 * @package    Patchwork
 * @subpackage Security
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * PHPIDS plugin
 *
 * requires
 * - that config includes a "phpids" section
 * - IDS to be autoloadable (e.g. in library)
 * <code>
 * phpids.input = REQUEST,GET,POST,COOKIE
 * phpips.skippedMCA.one = module/controller/action
 * phpips.skippedMCA.two = mymodule/mycontroller/myaction
 * phpids.impact_threshold = 10
  phpids.impact_controller = error
  phpids.impact_action = badrequest
  phpids.General.filter_type     = xml
  phpids.General.base_path       = LIBRARY_PATH "/IDS/"
  phpids.General.use_base_path   = false
  phpids.General.filter_path     = LIBRARY_PATH "/IDS/default_filter.xml"
  phpids.General.tmp_path        = TMP_PATH
  phpids.General.scan_keys       = false
  phpids.General.HTML_Purifier_Path	= vendors/htmlpurifier/HTMLPurifier.auto.php
  phpids.General.HTML_Purifier_Cache = vendors/htmlpurifier/HTMLPurifier/DefinitionCache/Serializer
  phpids.General.html[]          = POST.__wysiwyg
  phpids.General.json[]          = POST.__jsondata
  phpids.General.exceptions[]    = GET.__utmz
  phpids.General.exceptions[]    = GET.__utmc
  phpids.General.min_php_version = 5.1.6
  phpids.Logging.path            = TMP_PATH "/logs/ids.log"
  phpids.Caching.caching         = file
  phpids.Caching.expiration_time = 600
  phpids.Caching.path            = TMP_PATH "/default_filter.cache"
 * </code>
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
     * default threshold to redirect to error page
     */
    const DEFAULT_IMPACT_THRESHOLD = 20;

    /**
     * config
     * @var Zend_Config
     */
    private $config;

    /**
     * scans the request
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->config =  Patchwork_Container::getBootstrapContainer()
            ->getApplicationConfig()
            ->{self::PHPIDS_CONFIG_SECTION};

        if($this->isSkippedMCA($request)) {
            return;
        }
        

        $init = IDS_Init::init();
        $init->setConfig($this->config->toArray());

        // run the ids monitor
        $ids = new IDS_Monitor($this->getInput(), $init);
        $result = $ids->run();
        // analyze the result
        if (!$result->isEmpty()) {
            $impact = $result->getImpact();
            // log the event
            $compositeLog = new IDS_Log_Composite();
            $compositeLog->addLogger(IDS_Log_File::getInstance($init));
            $compositeLog->execute($result);
            // for high impact, we redirect to error page
            if ($impact >= $this->getImpactTreshold()) {
                $this->redirectToErrorPage($request);
            }
        }
    }

    /**
     * check if the module//controller/action is skipped
     * 
     * @param Zend_Controller_Request_Abstract $request request
     * @return boolean
     */
    private function isSkippedMCA(Zend_Controller_Request_Abstract $request)
    {
        $mca = $request->getModuleName() . DIRECTORY_SEPARATOR
            . $request->getControllerName() . DIRECTORY_SEPARATOR
            . $request->getActionName();

        if(!isset($this->config->skippedMCA)) {
            return false;
        }

        return in_array($mca, $this->config->skippedMCA->toArray());
    }

    /**
     * get the input array to check
     * 
     * @return array
     */
    private function getInput()
    {
        $raw = array('REQUEST', 'GET', 'POST', 'COOKIE');

        if(isset($this->config->input)) {
            $raw = explode(',', $this->config->input);
            foreach ($raw as $key => $val) {
                $raw[$key] = strtoupper(trim($val));
            }
        }
        
        $input = array();
        foreach ($raw as $var) {
            if(isset($GLOBALS['_'.$var])) {
                $input[$var] = $GLOBALS['_'.$var];
            }
        }
        
        return $input;
    }
    
    /**
     * get the threshold
     * @return int
     */
    private function getImpactTreshold()
    {
        $threshold = self::DEFAULT_IMPACT_THRESHOLD;
        if(isset($this->config->impact_threshold)) {
            $threshold = (int) $this->config->impact_threshold;
        }
        
        return $threshold;
    }
    
    /**
     * change request settings
     * 
     * @param Zend_Controller_Request_Abstract $request request
     */
    private function redirectToErrorPage(Zend_Controller_Request_Abstract $request)
    {
        $request->setControllerName($this->config->impact_controller);
        $request->setActionName($this->config->impact_action);
    }
}
