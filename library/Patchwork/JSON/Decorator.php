<?php
/**
 * generic JSON decorator
 *
 * @author     Daniel Pozzi
 * @package    Patchwork
 * @subpackage JSON
 */
class Patchwork_JSON_Decorator
{
    /**
     * json representation
     * @var string
     */
    private $json;

    /**
     * turn the argument into json, regards Patchwork_JSON_Serializable
     * 
     * @param mixed $subject
     */
    public function  __construct($subject)
    {
        if ($subject instanceof Patchwork_JSON_Serializable) {
            $this->json = $subject->toJSON();
        } elseif (is_object($subject) && method_exists($subject, 'toArray')) {
            /** @todo treat Doctrine_Records automaticallly */
            $this->json = json_encode((object)$subject->toArray());
        } elseif (is_array($subject)) {
            $this->json = $this->decorateArray($subject);
        } else {
            $this->json = json_encode($subject);
        }
    }

    /**
     * uses self recursively on arrays
     * 
     * @param array $data array to turn into json
     * @return string
     */
    private function decorateArray(array $data)
    {
        $return = array();
        foreach ($data as $entry) {
            $entry = new Patchwork_JSON_Decorator($entry);
            $return[] = (string)$entry;
        }

        return "[" . implode(',', $return) . "]";
    }

    /**
     * returns the json representation
     * @return string json
     */
    public function  __toString()
    {
        return $this->json;
    }
}