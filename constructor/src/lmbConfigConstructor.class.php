<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/constructor/src/lmbAbstractConstructor.class.php');
lmb_require('limb/core/src/lmbCollection.class.php');

class lmbConfigConstructor extends lmbAbstractConstructor
{
  protected $_config_template_file = 'settings/model_config.phtml';

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
  
  function getConfigFileName()
  {
    return $this->_table->getName() . '.conf.php';
  }

  function _getArraySource($array)
  {
    return var_export($array, true);
  }

  function create()
  {
    $vars = array();
    $vars['table_name'] = $this->_table->getName();

    $config_content = $this->_createContentFromTemplate($this->_config_template_file, $vars);
    $this->_project->addConfig($this->getConfigFileName(), $config_content);
  }
}
