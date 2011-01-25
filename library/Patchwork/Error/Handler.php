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
     * @var mixed
     */
    private $oldErrorHandler;
    
    /**
     * registers as error handler
     *
     */
    public function registerAsErrorHandler()
    {
        $this->oldErrorHandler = set_error_handler(array($this, 'handleError'));
    }

    /**
     * unregister as error handler
     * 
     */
    public function unregister()
    {
        set_error_handler($this->oldErrorHandler);
    }

    /**
     * handle error, throw exception
     * 
     * @param <type> $errno
     * @param <type> $errstr
     * @param <type> $errfile
     * @param <type> $errline
     * 
     * @throws ErrorException
     */
    public function handleError($errno, $errstr, $errfile, $errline )
    {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}