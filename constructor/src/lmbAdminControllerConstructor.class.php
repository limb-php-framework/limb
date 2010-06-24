<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/constructor/src/lmbAbstractConstructor.class.php');

class lmbAdminControllerConstructor extends lmbAbstractConstructor
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
    return 'Admin' . $this->_model_name . 'Controller.class.php';
  }

  protected function _getResultTemplatePath($name)
  {
    return 'admin_'.lmb_under_scores($this->_model_name).'/'.$name;
  }

  protected function _findDateTimeColumns()
  {
    $datetime_columns = array();
    foreach($this->_table->getColumns() as $column)
    {
      if($column->getType() === lmbDbTypeInfo::TYPE_INTEGER &&
         (strstr($column->getName(), 'time') || strstr($column->getName(), 'date')) &&
         !in_array($column->getName(), $this->_meta_fields))
      {
        $datetime_columns[] = $column;
      }
    }

    return $datetime_columns;
  }

  function create()
  {
    $vars = array(
      'model_name' => $this->_model_name,
      'datetime_columns' => $this->_findDateTimeColumns(),
    );
    $content = $this->_createContentFromTemplate('admin_controller/controller.phtml', $vars);
    $this->_project->addController($this->getControllerFileName(), $content);
  }
}