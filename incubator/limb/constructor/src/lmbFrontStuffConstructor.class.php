<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/constructor/src/lmbAbstractConstructor.class.php');

class lmbFrontStuffConstructor extends lmbAbstractConstructor
{  
  protected $_model_name;

  /**
   * @param lmbProjectConstructor $project
   * @param lmbDbInfo $database_info
   * @param lmbDbTableInfo $table
   * @param string $model_name
   */
  function __construct($project, $database_info, $table, $model_name = null, $templates_dir = null)
  {
    parent::__construct($project, $database_info, $table, $templates_dir);

    if(is_null($model_name))   
      $model_name = lmb_camel_case($table->getName());
      
    $this->_model_name = $model_name;
  }
  
  function getControllerFileName()
  {
    return $this->_model_name . 'Controller.class.php';
  } 
  
  protected function _getResultTemplatePath($name)
  {
    return lmb_under_scores($this->_model_name).'/'.$name;
  }
   
  function create()
  { 
    $vars = array(
      'model_name' => $this->_model_name,
      'model_url' => lmb_under_scores($this->_model_name),
      'columns' => $this->_table->getColumns(),      
    );   
    
    $content = $this->_createContentFromTemplate('front_for_model/controller.phtml', $vars);
    $this->_project->addController($this->getControllerFileName(), $content);        
           
    $content = $this->_createContentFromTemplate('front_for_model/display.phtml', $vars, false);
    $this->_project->addTemplate($this->_getResultTemplatePath('display.phtml'), $content);    
    
    $content = $this->_createContentFromTemplate('front_for_model/item.phtml', $vars, false);    
    $this->_project->addTemplate($this->_getResultTemplatePath('item.phtml'), $content);    
  }
}