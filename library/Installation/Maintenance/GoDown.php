<?php
/**
 * checks that files exists to set server to maintenance mode
 *
 *
 */
class Installation_Maintenance_GoDown extends Installation_Check
{
    /**
     * downtime
     * @var Installation_Maintenance
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

    public function testNoIndexBackup()
    {
        $this->checkIsNotFile($this->maintenance->getBackupFile());
    }

    public function testDowntimeFileExists()
    {
        $this->checkIsFile($this->maintenance->getDowntimeFile());
    }

    public function testIsWriteableIndex()
    {
        $this->checkIsWriteable($this->maintenance->getIndexFile());
    }
}