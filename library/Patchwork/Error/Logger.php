<?php
/**
 * Patchwork Error Logger
 *
 * use it to log any uncaught exception to the log and to give it a
 * reference number
 *
 * @author Daniel Pozzi
 */
class Patchwork_Error_Logger
{
    /**
     * log
     * @var Zend_Log
     */
    private $log;

    /**
     *
     * @var string
     */
    private $refCode;

    /**
     *
     * @param Zend_Log $log 
     */
    public function  __construct(Zend_Log $log)
    {
        $this->log = $log;
    }

    /**
     * handle an exception
     * 
     * @param Exception $e
     */
    public function handleException(Exception $e)
    {
        $this->makeRefCode($e);
        $this->log->log(
            $this->refCode.' '.$e->getMessage(),
            Zend_Log::ERR,
            get_object_vars($e)
        );
    }

    /**
     * creates a refcode in the style "ABCD:ABCD"
     * 
     * @param Exception $e exception
     * @return string
     */
    private function makeRefCode(Exception $e)
    {
        $firstPart = substr(0, 4, strtoupper(md5($e->getMessage())));
        $secondPart = substr(0, 4, strtoupper(md5(time())));

        $this->refCode = $firstPart . PATH_SEPARATOR . $secondPart;
    }

    /**
     * get the ref code
     * 
     * @return string
     */
    public function getRefCode()
    {
        return $this->refCode;
    }
}