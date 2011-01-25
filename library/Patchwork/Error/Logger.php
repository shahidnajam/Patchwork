<?php
/**
 * Patchwork Error Logger
 *
 * use it to log any uncaught exception to the log and to give it a
 * reference number
 *
 * - Remember to add a writer to Zend_Log, the container won't do it by itself.
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
        $firstPart = substr(strtoupper(md5($e->getMessage())), 0, 4);
        $secondPart = substr(strtoupper(md5(time())), 0, 4);

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