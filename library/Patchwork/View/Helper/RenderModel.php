<?php
/**
 * renderModel view helper
 *
 * @author     Daniel Pozzi
 * @package    Patchwork
 * @subpackage Views
 */

/**
 * renderModel view helper
 *
 * allows to render a model directly using a view, a view file must be provided
 *
 * @author     Daniel Pozzi
 * @package    Patchwork
 * @subpackage Views
 */
class Patchwork_View_Helper_RenderModel extends Zend_View_Helper_Abstract
{
    /**
     * loads a model-context specific view and assigns a variable named after
     * the model's class
     *
     * @param Doctrine_Record $model
     * @param string          $context
     * @param string          $args
     * @param boolean         $returnView return the view object, dont render
     * 
     * @return string|Zend_View
     */
    public function renderModel(
        Doctrine_Record $model,
        $context,
        array $args = null,
        $returnView = false
    ){
        if(!$args)
            $args = array();
        $args[get_class($model)] = $model;

        $path = self::getViewPath(get_class($model), $context);
        $view = new Zend_View();
        $view->setScriptPath( dirname($path) );
        $view->assign($args);
        
        if($returnView)
            return $view;
        else
            return $view->render( basename($path) );
    }

    /**
     * get view file including path
     *
     * @param string $modelName name of the model
     * @param string $context context, concatenated with underscore to filename
     * @param string $suffix file suffix
     * 
     * @return string
     */
    public static function getViewPath(
        $modelName,
        $context = '',
        $suffix = null
    ){
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'views'
            . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR
            . strtolower($modelName);
        if($context != '')
            $path .= '_'.$context;
        if($suffix == null)
            $suffix = '.phtml';
        
        $path .= $suffix;

        return $path;
    }
}