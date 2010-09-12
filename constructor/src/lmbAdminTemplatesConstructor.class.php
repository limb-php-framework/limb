<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/constructor/src/lmbAbstractConstructor.class.php');
lmb_require('limb/constructor/src/lmbFormConstructorHelper.class.php');

class lmbAdminTemplatesConstructor extends lmbAbstractConstructor
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

  protected function _getResultTemplatePath($name)
  {
    return 'admin_'.lmb_under_scores($this->_model_name).'/'.$name;
  }

  protected function _getColumnsForDisplay()
  {
    $columns_for_display = array();
    foreach($this->_table->getColumns() as $column)
    {
      if($column->getType() === lmbDbTypeInfo::TYPE_CLOB ||
        (($column->getType() === lmbDbTypeInfo::TYPE_CHAR || $column->getType() === lmbDbTypeInfo::TYPE_VARCHAR) && 255 < $column->getSize()))
      continue;

      $columns_for_display[] = $column;
    }

    return $columns_for_display;
  }

  protected function _getFieldsForDisplay($columns_for_display)
  {
    $fields_for_display = array();
    $this->_apply_publish_templates = '';

    foreach($columns_for_display as $column)
    {
      $column_name = $column->getName();

      if('is_published' == $column_name)
      {
        $fields_for_display[$column_name] = '<? echo $item->getIsPublished() ? "Опубликована" : "Скрыта"; ?>';
        $this->_apply_publish_templates = '<? if($item->getIsPublished()) [ ?>[[apply template="object_action_unpublish" item="[$item]" icon="page_red"/]]<? ] else [ ?>[[apply template="object_action_publish" item="[$item]" icon="page_green"/]]<? ] ?>';
      }
      elseif(strstr($column_name, 'time') || strstr($column_name, 'date'))
        $fields_for_display[$column_name] = '[$item.' . $column_name . '|date:"d.m.Y"]';
      else
        $fields_for_display[$column_name] = '[$item.' . $column_name . ']';
    }

    return $fields_for_display;
  }

  function create()
  {
    $columns = $this->_table->getColumns();
    $columns_for_display = $this->_getColumnsForDisplay();

    $form_constructor = new lmbFormConstructorHelper($columns);

    $vars = array(
      'model_name' => $this->_model_name,
      'model_url' => lmb_under_scores($this->_model_name),
      'columns' => $columns_for_display,
      'column_names' => array_diff($form_constructor->getColumnsNames(), $this->_meta_fields),
      'form_fields' => $form_constructor->createFormFields($columns),
      'fields_for_display' => $this->_getFieldsForDisplay($columns_for_display),
      'apply_publish_templates' => $this->_apply_publish_templates,
    );

    $content = $this->_createContentFromTemplate('admin_templates/display.phtml', $vars, false);
    $this->_project->addTemplate($this->_getResultTemplatePath('display.phtml'), $content);

    $content = $this->_createContentFromTemplate('admin_templates/form_fields.phtml', $vars, false);
    $this->_project->addTemplate($this->_getResultTemplatePath('include/form_fields.phtml'), $content);

    $content = $this->_createContentFromTemplate('admin_templates/create.phtml', $vars, false);
    $this->_project->addTemplate($this->_getResultTemplatePath('create.phtml'), $content);

    $content = $this->_createContentFromTemplate('admin_templates/edit.phtml', $vars, false);
    $this->_project->addTemplate($this->_getResultTemplatePath('edit.phtml'), $content);

    $content = $this->_createContentFromTemplate('admin_templates/delete.phtml', $vars, false);
    $this->_project->addTemplate($this->_getResultTemplatePath('delete.phtml'), $content);
  }
}