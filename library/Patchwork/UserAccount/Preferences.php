<?php
/**
 *
 */
interface Patchwork_UserAccount_Preferences
{
    /**
     * @return array Patchwork_Preference[]
     */
    function getPreferences();

    /**
     * @return Patchwork_Preference
     */
    function getPreference($preferenceName);
}