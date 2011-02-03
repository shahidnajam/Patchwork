<?php
/**
 * Service Log Domain events
 *
 * -proxy to Zend_Log
 * 
 * - the writer passes events with DOMAIN priority to a model which handles
 * the event (write to db, else)
 *
 * @package    Patchwork
 * @subpackage DomainLog
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
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
     * @param string $message    log message
     * @param string $entityType class name / model name
     * @param int    $entityId   model identifier (uuid etc)
     * @param int    $userId     id of the current user
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
            array(
                'entityType' => $entityType,
                'entityId'   => $entityId,
                'userId'     => $userId
            )
        );
    }
}