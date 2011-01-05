<?php
/**
 * checks that files exists to set server to maintenance mode
 *
 * @package    Installation
 * @subpackage Maintenance
 * @author     Daniel Pozzi
 */
class Installation_Maintenance_GoUp extends Installation_Check
{
    /**
     * downtime
     * @var Installation_Downtime
     */
    private $maintenance;

    /**
     * pass downtime
     *
     * @param Installation_Maintenance $maintenance
     */
    public function  __construct(Installation_Maintenance $maintenance)
    {
        $this->maintenance = $maintenance;
    }

    public function testForIndexBackup()
    {
        $this->checkIsFile($this->maintenance->getBackupFile());
    }

    public function testIsWriteableIndexFile()
    {
        $this->checkIsFile($this->maintenance->getIndexFile());
    }

    public function testIsWriteableDowntime()
    {
        $this->checkIsWriteable($this->maintenance->getDowntimeFile());
    }
}