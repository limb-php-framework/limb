<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/constructor/src/lmbAdminTemplatesConstructor.class.php');

class lmbAdminTreeTemplatesConstructor extends lmbAdminTemplatesConstructor
{
  protected $_meta_fields = array('ctime', 'utime', 'kind', 'parent_id', 'level', 'priority', 'is_published', 'path');

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

    $content = $this->_createContentFromTemplate('admin_tree_templates/display.phtml', $vars, false);
    $this->_project->addTemplate($this->_getResultTemplatePath('display.phtml'), $content);

    $content = $this->_createContentFromTemplate('admin_tree_templates/include/items_list.phtml', $vars, false);
    $this->_project->addTemplate($this->_getResultTemplatePath('include/items_list.phtml'), $content);

    $content = $this->_createContentFromTemplate('admin_tree_templates/replace.phtml', $vars, false);
    $this->_project->addTemplate($this->_getResultTemplatePath('replace.phtml'), $content); 

    $content = $this->_createContentFromTemplate('admin_tree_templates/include/form_fields.phtml', $vars, false); 
    $this->_project->addTemplate($this->_getResultTemplatePath('include/form_fields.phtml'), $content);

    $content = $this->_createContentFromTemplate('admin_tree_templates/create.phtml', $vars, false);
    $this->_project->addTemplate($this->_getResultTemplatePath('create.phtml'), $content);

    $content = $this->_createContentFromTemplate('admin_tree_templates/edit.phtml', $vars, false);
    $this->_project->addTemplate($this->_getResultTemplatePath('edit.phtml'), $content);

    $content = $this->_createContentFromTemplate('admin_tree_templates/delete.phtml', $vars, false);
    $this->_project->addTemplate($this->_getResultTemplatePath('delete.phtml'), $content);
  }

  protected function _createContentFromTemplate($template, $vars, $tags_needed = true)
  {
    $template = $this->_createMacroTemplate($template); 
    $template->setVars($vars);
    $content = $template->render();
    $content = $this->_preparePHPTags($content);

    return $content; 
  }

  protected function _preparePHPTags($content)
  {
    $content = str_replace('<%=', '<?', $content);
    $content = str_replace('=%>', '?>', $content);

    return $content;
  }
}

