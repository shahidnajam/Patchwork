<?php
/**
 * Patchwork_Preference_Service
 *
 *
 */
interface Patchwork_Preference_Service
{
    /**
     * get a preference by key
     * 
     * @param string $preferenceName key
     * 
     * @return Patchwork_Preference
     */
    function getApplicationPreference($preferenceName);

    /**
     * get a user preference by user and key
     *
     * @param Patchwork_UserAccount_Preferences $user
     * @param string                            $preferenceName key
     * 
     * @return Patchwork_Preference
     */
    function getUserPreference(
        Patchwork_UserAccount_Preferences $user,
        $preferenceName
    );

    /**
     * set an application preference
     * 
     * @param string $preferenceName
     * @param mixed  $value
     * 
     * @return Patchwork_Preference
     */
    function setApplicationPreference($preferenceName, $value);

    /**
     * set a user preference
     * 
     * @param Patchwork_UserAccount_Preferences $user
     * @param string                            $preferenceName key
     * @param mixed                             $value
     * 
     * @return Patchwork_Preference
     */
    function setUserPreference(
        Patchwork_UserAccount_Preferences $user,
        $preferenceName,
        $value
    );
}