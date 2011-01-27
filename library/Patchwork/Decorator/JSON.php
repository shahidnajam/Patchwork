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
     * turn the argument into json, regards Patchwork_JSON_Serializable
     * 
     * @param mixed $subject
     */
    public function  __construct($subject)
    {
        if ($subject instanceof Patchwork_JSON_Serializable) {
            $this->json = $subject->toJSON();
        } elseif (is_object($subject) && method_exists($subject, 'toArray')) {
            /** @todo treat Doctrine_Records automatically */
            $this->json = json_encode((object)$subject->toArray());
        } else {
            $this->json = json_encode($subject);
        }
    }

    /**
     * returns the json representation
     * 
     * @return string json
     */
    public function  __toString()
    {
        return $this->json;
    }
}