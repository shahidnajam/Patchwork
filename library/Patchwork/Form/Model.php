<?php
/**
 * Patchwork Form Model
 *
 * @category Library
 * @package  Patchwork
 * @author   Daniel Pozzi
 */
interface Patchwork_Form_Model
{
    /**
     * return the model data as assoc array to populate a form
     * 
     * @return array
     */
    function getDataForForm();

    /**
     * set properties
     * 
     * @param array $data assoc array
     */
    function setDataFromForm(array $data);
}