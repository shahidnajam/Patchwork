<?php
/**
 * Patch
 *
 * @category Application
 * @package  Service
 * @author   Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * TokenTriggered interface
 *
 * @category Application
 * @package  Service
 * @author   Daniel Pozzi <bonndan76@googlemail.com>
 */
interface TokenTriggered
{
    /**
     * method to start the service using a token's data
     *
     * @param Token $token pass an instance of Token
     * @return self
     */
    function startWithToken(Token $token);
}