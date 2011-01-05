<?php

/**
 * Migration class
 *
 * implement methods up0, up1 ... upN and downN ... down0
 * 
 */
abstract class Installation_Migration
{
    const UP_PREFIX = 'up';
    const DOWN_PREFIX = 'down';
    
    /**
     * PDO resource
     * @var PDO
     */
    private $connection;

    /**
     * where to start from
     * @var int
     */
    private $from;

    /**
     * where to end
     * @var int
     */
    private $to;

    /**
     * @var int
     */
    private $migrationStage;

    /**
     * constructor needs an opne connection
     * 
     * @param resource $connection
     */
    public function  __construct (PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * reads "from" and "to" from command line
     */
    public function setCLIArguments()
    {
        $arguments = getopt("from:to:");
        if (is_int($arguments['from'])) {
           $this->from = $arguments['from'];
        }

        if (is_int($arguments['to'])) {
           $this->to = $arguments['to'];
        }
    }

    /**
     * set the number to start from
     * 
     * @param int $from starting number
     * @return Installation_Migration
     */
    public function from($from)
    {
        $this->from = (int) $from;
        return $this;
    }

    /**
     * set the end stage
     * 
     * @param int $to
     * @return Installation_Migration
     */
    public function to($to)
    {
        $this->to = (int) $to;
        return $this;
    }

    /**
     * get the connection
     * @return PDO
     */
    protected function getConnection()
    {
        return $this->connection;
    }

    /**
     * get the method prefix
     * 
     * @param boolean $reverse
     * @return string
     */
    private function getMethodPrefix($reverse)
    {
        if ($reverse) {
            $prefix = self::DOWN_PREFIX;
        } else {
            $prefix = self::UP_PREFIX;
        }

        return $prefix;
    }

    /**
     * check if processing can continue at stage
     * 
     * @param int     $stage   stage no
     * @param boolean $reverse reverse (downwards = true)
     * @return boolean
     */
    private function mayContinue($stage, $reverse)
    {
        if ($stage < 0) {
            return false;
        }
        
        $method = method_exists($this, $this->getMethodPrefix($reverse).$stage);
        if ($reverse) {
            $inLimit = $stage >= $this->to;
        } else {
            $inLimit = $stage <= $this->to;
        }

        return $method && $inLimit;
    }
    
    /**
     * migration upwards
     *
     * @return Installation_Migration
     */
    public function migrate()
    {
        if ($this->from === NULL) {
            $this->from = 0;
        }
        if ($this->to === NULL) {
            $this->to = $this->getHighestStage();
        }

        return $this->process();
    }

    /**
     * revert migration
     *
     *
     */
    public function degrade()
    {
        if($this->from === NULL) {
            $this->from = $this->getHighestStage();
        }
        if($this->to === NULL) {
            $this->to = 0;
        }

        return $this->process(true);
    }

    /**
     * process stages
     * 
     * @param boolean $reverse downwards
     * @return Installation_Migration
     */
    private function process($reverse = false)
    {
        $i = $this->from;
        while ($this->mayContinue($i, $reverse)) {
            $methodName = $this->getMethodPrefix($reverse).$i;
            $message = $this->$methodName();
            $this->migrationStage = $i;
            if($reverse) {
                echo 'Degrade from stage ' .$i . '. '. $message . PHP_EOL;
                $i--;
            } else {
                echo 'Migrated to stage ' .$i . '. '. $message . PHP_EOL;
                $i++;
            }
        }

        if($this->to != null) {
            if($reverse && $this->to < $i) {
                throw new Exception(
                    'Could not degrade to '.$this->to.', stopped at '.$i
                );
            } elseif (!$reverse && $this->to > $i) {
                throw new Exception(
                    'Could not migrate to '.$this->to.', stopped at '.$i
                );
            }
        }
        
        return $this;
    }

    /**
     * get the highest available stage
     * @return int
     */
    public function getHighestStage()
    {
        $i = 0;
        while (method_exists($this,  'up' . $i)) {
            $i++;
        }
        $i--;
        return $i;
    }
    
    /**
     * get the migration stage
     * 
     * @return int
     */
    public function getMigrationStage()
    {
        return $this->migrationStage;
    }

    /**
     * init script
     */
    abstract function up0();

    /**
     * drop db or drop all tables, ...
     */
    abstract function down0();
}