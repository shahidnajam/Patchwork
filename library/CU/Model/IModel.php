<?php
/**
 * Model interface
 */
interface CU_Model_IModel {
    /**
     * Are values of this model valid?
     * @return bool
     */
    public function isValid();

    /**
     * Get any error messages related to validation
     *
     * Must return an array of key => value pairs where the
     * key is the property of the model the message is for
     * and the value is the actual message
     *
     * @return array
     */
    public function getMessages();

    /**
     * Set a public property of the model
     * @param string $property
     * @param mixed $value
     */
    public function set($property, $value);

    /**
     * Get a public property of the model
     * @param string $property
     * @return mixed
     */
    public function get($property);
}
