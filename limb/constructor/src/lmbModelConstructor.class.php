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
    $vars['table_name'] = $this->_table->getName();

    $vars['lazy_attributes'] = $this->_getLazyAttributes($this->_table);

    $relations_exist = false;

    $rels_constructor = $this->_createARRelationsConstructor();

    $rels_has_many = $rels_constructor->getRelationsHasManyFor($this->_table->getName());
    if(count($rels_has_many))
    {
      $vars['has_many'] = $this->_getArraySource($rels_has_many);
      $relations_exist = true;
    }

    $rels_many_belongs = $rels_constructor->getRelationsManyBelongsFor($this->_table->getName());
    if(count($rels_many_belongs))
    {
      $vars['many_belongs_to'] = $this->_getArraySource($rels_many_belongs);
      $relations_exist = true;
    }

    $rels_has_one = $rels_constructor->getRelationsHasOneFor($this->_table->getName());
    if(count($rels_has_one))
    {
      $vars['has_one'] = $this->_getArraySource($rels_has_one);
      $relations_exist = true;
    }

    $rels_belongs = $rels_constructor->getRelationsBelongsFor($this->_table->getName());
    if(count($rels_belongs))
    {
      $vars['belongs_to'] = $this->_getArraySource($rels_belongs);
      $relations_exist = true;
    }

    $rels_many_to_many = $rels_constructor->getRelationsManyToManyFor($this->_table->getName());
    if(count($rels_many_to_many))
    {
      $vars['has_many_to_many'] = $this->_getArraySource($rels_many_to_many);
      $relations_exist = true;
    }

    $vars['relations_exist'] = isset($vars['relations_exist']) ? ($vars['relations_exist'] || $relations_exist) : $relations_exist;

    $model_content = $this->_createContentFromTemplate($this->_model_template_file, $vars);
    $this->_project->addModel($this->getModelFileName(), $model_content);
  }

  protected function _getLazyAttributes($table)
  {
    $columns = $table->getColumns();

    $lazy_attributes = array();
    foreach($columns as $column)
    {
      if($column->getType() === lmbDbTypeInfo::TYPE_CLOB ||
        (($column->getType() === lmbDbTypeInfo::TYPE_CHAR || $column->getType() === lmbDbTypeInfo::TYPE_VARCHAR) && 256 <= $column->getSize()))
      {
        $lazy_attributes[] = $column->getName();
      }
    }

    return $lazy_attributes;
  }

  function _createARRelationsConstructor()
  {
    return new lmbARRelationConstructor(
      $this->_database_info,
      new lmbARRelationAggrementsResolver()
    );
  }

  function _getArraySource($array)
  {
    return var_export($array, true);
  }
}