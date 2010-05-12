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
class App_View_Helper_RenderModel extends Zend_View_Helper_Abstract
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
        
        $view = new Zend_View();
        $view->assign($args);
        return $view->render(
            $this->getViewPath(get_class($model), $context)
        );
        
    }

    /**
     * get view fiel including path
     *
     * @param string $modelName
     * @param string $context
     * 
     * @return string
     */
    public function getViewPath($modelName, $context = '')
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