<?php
/**
 * Downtime management
 *
 * @package Installation
 * @author  Daniel Pozzi <bonndan76@googlemail.com>
 */
class Installation_Maintenance
{
    const DOWNTIME_INDEX_FILE = 'downtime.php';
    const INDEX_BACKUP_FILE   = 'index.php.sav';
    const INDEX_FILE          = 'index.php';

    /**
     * @var string
     */
    public $publicPath;

    /**
     * constructor
     * 
     * @param string $publicPath path
     */
    public function  __construct($publicPath)
    {
        $this->publicPath = realpath($publicPath);
    }

    public function getDowntimeFile()
    {
        return $this->publicPath . DIRECTORY_SEPARATOR . self::DOWNTIME_INDEX_FILE;
    }

    public function getBackupFile()
    {
        return $this->publicPath . DIRECTORY_SEPARATOR . self::INDEX_BACKUP_FILE;
    }

    public function getIndexFile()
    {
        return $this->publicPath . DIRECTORY_SEPARATOR . self::INDEX_FILE;
    }

    /**
     * down: replaces the index.php file in publicPath
     *
     * 
     */
    public function down()
    {
        $check = new Installation_Maintenance_GoDown($this);
        $check->run();
        if ($check->isWithoutErrors()) {
            copy(
                $this->getIndexFile(),
                $this->getBackupFile()
            );

            copy(
                $this->getDowntimeFile(),
                $this->getIndexFile()
            );
            return true;
        } else {
            echo 'Could not complete go-down: '.PHP_EOL;
            echo $check->__toString();
            return false;
        }
    }

    /**
     * up: re-replaces the index.php file in publicPath
     *
     * @return boolean
     */
    public function up()
    {
        $check = new Installation_Maintenance_GoUp($this);
        $check->run();
        if ($check->isWithoutErrors()) {
            copy(
                $this->getBackupFile(),
                $this->getIndexFile()
            );
            unlink($this->getBackupFile());
            return true;
        } else {
            echo 'Could not complete go-up: '.PHP_EOL;
            echo $check->__toString();
            return false;
        }
    }
}
