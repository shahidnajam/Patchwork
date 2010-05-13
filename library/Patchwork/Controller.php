<?php
/**
 * used as a flag
 * 
 * 
 */
interface Patchwork_Controller
{
    /**
     * returns a model
     *
     * @param boolean $withPrimaryKey
     */
    public function initModel($withPrimaryKey = true);
}