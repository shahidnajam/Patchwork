<?php

/**
 * generic JSON decorator
 *
 * @author     Daniel Pozzi
 * @package    Patchwork
 * @subpackage JSON
 */
class Patchwork_Decorator_JSON
{

    /**
     * json representation
     * @var string
     */
    private $json;

    /**
     * serialization of doctrine collections
     * 
     * @param Doctrine_Collection $coll collection
     * @return string
     */
    private function serializeCollection($coll)
    {
        $res = array();
        foreach ($coll as $record) {
            $res[] = new self($record);
        }

        return "[" . implode(',', $res) . "]";
    }

    /**
     * turn the argument into json
     *
     * regards Patchwork_JSON_Serializable
     * regards Doctrine_Collections, which should not appear if StorageService
     * is used
     * 
     * @param mixed $subject
     */
    public function __construct($subject)
    {
        if ($subject instanceof Patchwork_Serializable_JSON) {
            $this->json = $subject->toJSON();
        } elseif ($subject instanceof Doctrine_Collection) {
            $this->json = $this->serializeCollection($subject);
        } elseif (is_array($subject) && is_object(current($subject))) {
            $this->json = $this->serializeCollection($subject);
        } else  {
            $this->json = json_encode($subject);
        }
    }

    /**
     * returns the json representation
     * 
     * @return string json
     */
    public function __toString()
    {
        return $this->json;
    }

}