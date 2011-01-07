<?php
/**
 * interface for a token model
 *
 * @package    Patchwork
 * @subpackage Token
 * @author     Daniel Pozzi
 */
interface Patchwork_Token
{
    /**
     * set the service to be triggered
     *
     * @param Patchwork_Token_Triggered $service service instance
     */
    function setTriggeredService(Patchwork_Token_Triggered $service);

    /**
     * get the instance of the service to be triggered
     * @return Patchwork_Token_Triggered
     */
    function getTriggeredService();

    /**
     * set contextual data
     * @param array $context
     */
    function setContext(array $context);

    /**
     * get contextual data
     * 
     */
    function getContext();

    /**
     * get the hash
     * @return string
     */
    function getHash();
    
    /**
     * set mutliple usage
     * @param boolean $flag flag
     */
    function setMultipleUse($flag);

    /**
     * check if token can be use more than once
     * @return boolean
     */
    function isMultiplyUsable();
}