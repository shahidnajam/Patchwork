<?php
/**
 * Patch
 *
 * @category Application
 * @package  Service
 * @author   Daniel Pozzi <bonndan76@googlemail.com>
 */

/**
 * Patchwork_Token_Triggered interface
 *
 * @category Application
 * @package  Service
 * @author   Daniel Pozzi <bonndan76@googlemail.com>
 */
interface Patchwork_Token_Triggered
{
    /**
     * method to start the service using a token's data
     *
     * @param Patchwork_Token $token pass an instance of Token
     * @return self
     */
    function startWithToken(Patchwork_Token $token);
}