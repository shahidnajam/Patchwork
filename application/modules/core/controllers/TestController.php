<?php
/**
 * 
 */
class Core_TestController extends Patchwork_Controller_Action
{
    /**
     * @return Patchwork_Preference_Service
     */
    private function getPreferenceService()
    {
        return $this->getContainer()->getInstance('Patchwork_Preference_Service');
    }

    public function indexAction()
    {
        $this->view->preferences = $this->getPreferenceService()
            ->getApplicationPreferences();
    }
}