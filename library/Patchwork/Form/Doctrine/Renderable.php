<?php

/**
 * Patchwork_Doctrine_FormRenderable
 *
 * The interface which has to be implemented of models to work with the ModelForm
 *
 * @author     Daniel Pozzi <bonndan76@googlemail.com>
 * @category   Library
 * @package    Patchwork
 * @subpackage Doctrine
 */
interface Patchwork_Form_Doctrine_Renderable
{
    /**
     * returns an array of field names which must not occur in form
     *
     * @return array
     */
    public function getIgnoredColumns();

}