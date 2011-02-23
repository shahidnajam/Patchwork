<?php
/**
 * Patchwork
 *
 *
 * 
 */

/**
 * Core_Service_PreferenceService
 *
 * @category   Application
 * @package    Core
 * @subpackage Service
 * @author     Daniel Pozzi
 */
class Core_Service_PreferenceService implements Patchwork_Preference_Service
{
    /**
     *
     * @param Patchwork_Storage_Service $service 
     */
    public function  __construct(Patchwork_Storage_Service $service)
    {
        $this->storageService = $service;
    }

    public function  getApplicationPreference($preferenceName)
    {
        return $this->storageService->findWhere(
            'Core_Model_Preference',
            array('name' => $preferenceName, 'user_id' => null)
        );
    }

    /**
     * save an application preference
     * 
     * @param <type> $preferenceName
     * @param <type> $value
     * @return unknown
     */
    public function  setApplicationPreference($preferenceName, $value)
    {
        $pref = $this->getApplicationPreference($preferenceName);
        if(!$pref) {
            $pref = new Core_Model_Preference;
            $pref->preference = $preferenceName;
        }

        $pref->value = $value;
        return $this->storageService->save($pref);
    }

    /**
     *
     * @param Patchwork_UserAccount_Preferences $user
     * @param <type> $preferenceName
     * @return Core_Model_Preference
     */
    public function getUserPreference(Patchwork_UserAccount_Preferences $user, $preferenceName)
    {
        return $user->getPreference($preferenceName);
    }

    /**
     *
     * @param Patchwork_UserAccount_Preferences $user
     * @param string $preferenceName
     * @param mixed  $value
     * @return unknown
     */
    public function  setUserPreference(Patchwork_UserAccount_Preferences $user, $preferenceName, $value)
    {
        $pref = $this->getUserPreference($user, $preferenceName);
        if (!$pref) {
            $pref = new Core_Model_Preference;
            $pref->preference = $preferenceName;
        }
        $pref->value = $value;
        return $this->storageService->save($pref);
    }
}