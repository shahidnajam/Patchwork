<?php
/**
 * renderModel view helper
 *
 * allows to render a model directly using a view
 *
 * @author     Daniel Pozzi
 * @package    Patchwork
 * @subpackage Views
 */
class Patchwork_View_Helper_RenderModel extends Zend_View_Helper_Abstract
{
    /**
     *
     * @param Doctrine_Record $model
     * @param string          $context
     * @param string          $args
     * 
     * @return string
     */
    public function renderModel(
        Doctrine_Record $model,
        $context,
        array $args = null
    ){
        if(!$args)
            $args = array();
        $args['model'] = $model;

        $path = self::getViewPath(get_class($model), $context);
        $view = new Zend_View();
        $view->setScriptPath( dirname($path) );
        $view->assign($args);
        return $view->render( basename($path) );
    }

    /**
     * get view file including path
     *
     * @param string $modelName
     * @param string $context
     * 
     * @return string
     */
    public static function getViewPath($modelName, $context = '')
    {
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'views'
            . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR
            . strtolower($modelName);
        if($context != '')
            $path .= '_'.$context;
        $path .= '.phtml';

        return $path;
    }
}