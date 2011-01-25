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
     * constructor registers itself as error handler
     *
     */
    public function __construct()
    {
        set_error_handler($this, 'handleError');
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
        $this->throwException($errno, $errstr, $errfile, $errline);
        return false;
    }

    private function throwException($errno, $errstr, $errfile, $errline)
    {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}