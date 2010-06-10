<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/constructor/src/lmbAbstractConstructor.class.php');
lmb_require('limb/constructor/src/lmbARRelationConstructor.class.php');
lmb_require('limb/constructor/src/lmbARRelationAggrementsResolver.class.php');
lmb_require('limb/core/src/lmbCollection.class.php');

class lmbModelConstructor extends lmbAbstractConstructor
{
  protected $_model_template_file = 'model/model.phtml';
  protected $_test_template_file = 'model/test.phtml';

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

  function getModelFileName()
  {
    return $this->_model_name . '.class.php';
  }

  function getTestFileName()
  {
    return '/model/'.$this->_model_name . 'Test.class.php';
  }

 
  function create($vars = null)
  {
    if(empty($vars))
      $vars = array();

    $vars['model_name'] = $this->_model_name;
    $model_content = $this->_createContentFromTemplate($this->_model_template_file, $vars);
    $this->_project->addModel($this->getModelFileName(), $model_content);

    $test_content = $this->_createContentFromTemplate($this->_test_template_file, $vars);
    $this->_project->addTest($this->getTestFileName(), $test_content);
  }
}