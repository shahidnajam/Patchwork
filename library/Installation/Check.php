<?php
/**
 * Installation check
 *
 * check an installation
 *
 * @package Installation
 * @author  Daniel Pozzi <bonndan@googlemail.com>
 */
abstract class Installation_Check
{
    /**
     * application config object
     * @var Zend_Config
     */
    protected $config;

    /**
     * array of checks and their result
     */
    protected $checkResults = array();
    
    private $reportTableClass = 'report';
    
    /**
     * set a config object
     * 
     * @param Zend_Config $config configuration
     * @return Installation_Check
     */
    public function setConfig(Zend_Config $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * log successful check
     *
     * @param string $message
     * @return true
     */
    private function addSuccess($message)
    {
        $this->checkResults[] = array(true, $message);
        return true;
    }

    /**
     * log failed check message
     * 
     * @param string $message
     * @param string $userMessage
     * 
     * @return false
     */
    private function addFailure($message,  $userMessage = '')
    {
        $this->checkResults[] = array(false, $message,  $userMessage);
        return false;
    }


    /**
     * check that a directory exists
     * 
     * @param string $dir directory location
     * @return boolean
     */
    protected function checkIsDir($dir, $message = '')
    {
        if (is_dir($dir)) {
            return $this->addSuccess($dir . ' is a directory');
        } else {
            return $this->addFailure($dir . ' is not a directory', $message);
        }
    }

    /**
     * check that a file exists
     *
     * @param string $filename file location
     * @return boolean
     */
    protected function checkIsFile($filename, $message = '')
    {
        if (file_exists($filename)) {
            return $this->addSuccess($filename . ' is a file');
        } else {
            return $this->addFailure($filename . ' is not a file', $message);
        }
    }

    /**
     * check that a file not exists
     *
     * @param string $filename file location
     * @return boolean
     */
    protected function checkIsNotFile($filename, $message = '')
    {
        if (!file_exists($filename)) {
            return $this->addSuccess($filename . ' is not present');
        } else {
            return $this->addFailure($filename . ' is present', $message);
        }
    }

    /**
     * check that a file or dir is writeable
     * @param string $filename
     */
    protected function checkIsWriteable ($filename,  $message = '')
    {
        if (is_writeable($filename)) {
            return $this->addSuccess($filename . ' is writeable');
        } else {
            return $this->addFailure($filename . ' is not writeable', $message);
        }
    }

    /**
     * check for a PDO connection
     * 
     * @param <type> $dsn
     * @param <type> $username
     * @param <type> $passwd
     * @return boolean
     */
    protected function checkIsPDOConnectableDSN (
        $dsn,
        $username = NULL,
        $passwd = NULL
    ) {

        try {
            $pdo = new PDO($dsn, $username, $passwd);
        } catch (PDOException $e) {
            
        }

        if ($pdo instanceof PDO) {
            return $this->addSuccess($dsn . ' connected');
        } else {
            return $this->addFailure(
                $dsn . ' not connected: ' . $e->getMessage()
            );
        }
    }

    /**
     *
     * @param <type> $server
     * @param <type> $username
     * @param <type> $password
     * @param <type> $new_link
     * @param <type> $client_flags 
     */
    protected function checkIsMysqlConnectable(
        $server,
        $username,
        $password = '',
        $new_link = null,
        $client_flags = null
    ) {
        $con = mysql_connect($server, $username, $password, $new_link, $client_flags);
    }

    /**
     * run the checks
     */
    public final function run()
    {
        $methods = get_class_methods($this);
        foreach ($methods as $method) {
            if(strpos($method, 'test') === 0) {
                $this->$method();
            }
        }
    }

    /**
     * prints a html table with the results
     * 
     */
    public function  toHTML()
    {
        $buffer = '<table class="'.$this->reportTableClass.'">';
        foreach ($this->checkResults as $result) {
            $color = ($result[0])?'green':'red';
            $buffer .= '<tr>';
            $buffer .= '<td style="background-color: '.$color.'">';
            $buffer .= ($result[0])?'OK':'FAIL';
            $buffer .= '</td>';
            $buffer .= '<td>';
            $buffer .= $result[1];
            $buffer .= '</td>';
            $buffer .= '<td>';
            $buffer .= (isset($result[2]))?$result[2]:'';
            $buffer .= '</td>';
            $buffer .= '</tr>';
        }
        $buffer .= '</table>';

        return $buffer;
    }

    /**
     * CLI output
     * @return string
     */
    public function __toString()
    {
        $buffer = 'Installation check:' . PHP_EOL;
        foreach ($this->checkResults as $result) {
            $sign = ($result[0])?'OK    ':'FAIL  ';
            $buffer .= $sign . ' ' . $result[1] . PHP_EOL;
        }

        return $buffer;
    }

    /**
     * all test passed OK?
     * 
     * @return boolean
     */
    public function isWithoutErrors()
    {
        foreach ($this->checkResults as $result) {
            if (!$result[0]) {
                return false;
            }
        }

        return true;
    }
}