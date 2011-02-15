<?php
/**
 * The Patchwork Error Handler turns ALL php errors into exceptions
 *
 * @link       http://php.net/manual/en/class.errorexception.php
 * @author     Daniel Pozzi
 * @package    Patchwork
 * @subpackage Error
 */
class Patchwork_Error_Handler
{
    /**
     * flag if it is the actual error handler
     * @var boolean
     */
    private $isErrorHandler = false;
    
    /**
     * registers as error handler
     *
     */
    public function registerAsErrorHandler()
    {
        set_error_handler(array($this, 'handleError'));
        $this->isErrorHandler = true;
        return $this;
    }

    /**
     * unregister as error handler
     * 
     */
    public function unregister()
    {
        $this->isErrorHandler = false;
    }

    /**
     * handle error, throw exception
     * 
     * @param int    $errno
     * @param string $errstr
     * @param string $errfile
     * @param int    $errline
     * 
     * @throws ErrorException
     */
    public function handleError($errno, $errstr, $errfile, $errline )
    {
        if (!$this->isErrorHandler) {
            return false;
        }
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}