#!/usr/bin/env php
<?php

/**
 * mother class for all, constructor options
 *
 * 
 */
abstract class DotElement
{

    /**
     *
     * @param array $options
     * @return self
     */
    public function __construct(array $options = null)
    {
        if (is_array($options))
            foreach ($options as $key => $val)
                $this->$key = $val;
    }

    /**
     * turn properties into dot
     * 
     * @return string
     */
    protected function _propsToDot()
    {
        $buffer = "";
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if ($key[0] != '_' && $value != null)
                $buffer .= $key . ' = "' . $value . '"' . "\n";
        }

        return $buffer;
    }

}

/**
 * dot edge
 *
 * 
 */
class DotEdge extends DotElement
{

    /**
     *
     * @var string
     */
    public static $lastEdgeDeclaration;
    /**
     * arrow head
     * @var string
     */
    public $arrowhead = 'none';
    /**
     * head label
     * @var string
     */
    public $headlabel;
    /**
     * tail label
     * @var string
     */
    public $taillabel;
    /**
     * target
     * @var string
     */
    protected $_to;
    /**
     * start
     * @var string
     */
    protected $_from;

    /**
     * construct node
     * 
     * @param string $from name of th starting node
     * @param string $to   name of the target node
     * @param array  $options
     *
     * @return DotEdge
     */
    public function __construct($from,
                                $to,
                                array $options = null
    )
    {
        $this->_from = $from;
        $this->_to = $to;

        parent::__construct($options);
    }

    /**
     * 
     * @return string
     */
    public function getEdgeDeclaration()
    {
        $buffer = "\nedge [\n";
        $buffer .= $this->_propsToDot();
        $buffer .= "]\n";

        return $buffer;
    }

    /**
     * magic to string method
     * 
     * @return string
     */
    public function __toString()
    {
        $buffer = "";
        $led = $this->getEdgeDeclaration();
        if ($led != self::$lastEdgeDeclaration){
            $buffer .= $led;
            self::$lastEdgeDeclaration = $led;
        }

        $buffer .= $this->_from . ' -> ' . $this->_to . "\n";
        return $buffer;
    }

}

/**
 * Node
 *
 * 
 */
class DotNode extends DotElement
{
    const HORIZONTAL_LINE = "|";

    /**
     * last node declaration
     * @var string
     */
    public static $lastNodeDeclaration;
    /**
     *
     * @var array
     */
    protected $_label;
    /**
     *
     * @var string
     */
    protected $_class;

    /**
     * shape
     * @var string
     */
    public $shape = "record";
    
    /**
     * fontsize
     * @var int
     */
    public $fontsize = 10;

    /**
     * turns a doctrine record into a dot label
     * 
     * @param  Doctrine_Record $record
     * @return self
     */
    public function setLabelFromModel(Doctrine_Record $record)
    {
        $this->_label[] = $this->_class = get_class($record);

        $tmp = array();
        foreach ($record->getData() as $acc => $key)
            $tmp[] = '+ ' . $acc;
        $tmp[] = ""; //just an empty line

        $this->_label[] = implode("\l", $tmp);
        return $this;
    }

    /**
     * return the necessay node declaration, only need if node props change
     *
     * @return string
     */
    public function getNodeDeclaration()
    {
        $buffer = "\nnode [\n";
        $buffer .= $this->_propsToDot();
        $buffer .= "]\n";

        return $buffer;
    }

    /**
     * render the node
     *
     * @return string
     */
    public function __toString()
    {
        $buffer = "\n";
        $lnd = $this->getNodeDeclaration();
        if ($lnd != self::$lastNodeDeclaration){
            $buffer .= $lnd;
            self::$lastNodeDeclaration = $lnd;
        }

        $buffer .= $this->_class . " [\n";
        $buffer .= 'label = "' . implode(self::HORIZONTAL_LINE, $this->_label) . "\"\n";
        $buffer .= "]\n";

        return $buffer;
    }

}

/**
 * Graph
 *
 *
 */
class DotGraph extends DotElement
{

    protected $_type;
    //digraph CE {
    public $rankdir = "LR";
    //public $nodesep = 0.75;
    public $ranksep = 0.75;
    public $overlap = "false";
    //fontsize=12;
    /**
     * all edges
     * @var array
     */
    protected $_edges = array();
    /**
     * all nodes
     * @var array
     */
    protected $_nodes = array();

    /**
     * render to string
     * 
     * @return string
     */
    public function __toString()
    {
        $buffer = "\ndigraph G {\n";
        $buffer .= $this->_propsToDot();
        

        foreach ($this->_nodes as $node)
            $buffer .= $node->__toString();

        foreach ($this->_edges as $key => $edges)
            foreach ($edges as $edge)
                $buffer .= $edge->__toString();

        $buffer .= "}\n";
        return $buffer;
    }

    /**
     * add a model
     * @param Doctrine_Record $model
     * 
     * @return self
     */
    public function addModel(Doctrine_Record $model)
    {
        $node = new DotNode();
        $node->setLabelFromModel($model);
        $this->_nodes[] = $node;
        foreach ($model->getTable()->getRelations() as $rel) {
            $this->addRelation(get_class($model), $rel);
        }
        return $this;
    }

    /**
     * add a relation
     * 
     * @param  Doctrine_Relation $relation
     * 
     * @return void
     */
    public function addRelation($class,
                                Doctrine_Relation $relation
    ){
        $from = $class;
        $to = $relation->getClass();
        if(isset($this->_edges[$to]) && isset($this->_edges[$to][$from]))
            return;
        
        $this->_edges[$from][$to] =  new DotEdge($from, $to);
    }

}
/**
 *
 */
require dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR
    . 'config' . DIRECTORY_SEPARATOR . 'initialize.php';
new sfYaml;

// Create application, bootstrap, and run
$application = new Zend_Application(
        APPLICATION_ENV,
        PATH_CONFIG . '/application.ini'
);

$application->bootstrap('Cli');

$conf = array(
    'data_fixtures_path' => PATH_ROOT . DS . 'application' . DS . 'doctrine' . DS . 'fixtures',
    'models_path' => PATH_ROOT . DS . 'application' . DS . 'models',
    'sql_path' => PATH_ROOT . DS . 'application' . DS . 'doctrine' . DS . 'sql',
    'yaml_schema_path' => PATH_ROOT . DS . 'application' . DS . 'doctrine' . DS . 'schemas',
    'migrations_path' => PATH_ROOT . DS . 'application' . DS . 'doctrine' . DS . 'migrations',
);
$cli = new Doctrine_Cli($conf);

/**
 * start
 */
$graph = new DotGraph();
Doctrine::loadModels($conf['models_path']);
$models = Doctrine::getLoadedModels();
foreach ($models as $model) {
    $graph->addModel(new $model);
}

echo $graph;



