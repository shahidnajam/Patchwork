<?php
/**
 * Log Domain events
 *
 * - the writer passes events with DOMAIN priority to a model which handles
 * the event (write to db, else)
 *
 * @author Daniel Pozzi
 */
class Patchwork_DomainLog_Service
{
    /**
     * log priority
     */
    const DOMAIN = 10;

    /**
     *
     * @var Zend_Log
     */
    private $log;

    /**
     * constructor
     * 
     * @param Zend_Log                   $log
     * @param Patchwork_DomainLog_Writer $writer 
     */
    public function  __construct (
        Zend_Log $log,
        Patchwork_DomainLog_Writer $writer
    ) {
        $log->addPriority('DOMAIN', 10);
        $log->addWriter($writer);

        $this->log = $log;
    }

    /**
     * log a domain event
     * 
     * @param string $message
     * @param string $entityType
     * @param int    $entityId
     * @param int    $userId
     */
    public function log(
        $message,
        $entityType = NULL,
        $entityId = NULL,
        $userId = NULL
    ) {
        $this->log->log(
            $message,
            self::DOMAIN,
            array($entityType, $entityId, $userId)
        );
    }
}