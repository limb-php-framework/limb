<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/constructor/tests/cases/lmbConstructorUnitTestCase.class.php');
lmb_require('limb/constructor/src/lmbAdminTemplatesConstructor.class.php');
lmb_require('limb/constructor/src/lmbARRelationAggrementsResolver.class.php');
lmb_require('limb/cli/src/lmbCliResponse.class.php');

class lmbAdminTemplatesConstructorTest extends lmbConstructorUnitTestCase
{
  protected $_admin_templates_path;

  function __construct()
  {
    parent :: __construct();

    $this->_admin_templates_path = dirname(__FILE__) . '/../../template/';
  }

  function _getProjectConstructor()
  {
    return new lmbProjectConstructor($this->dir_for_test_case, new lmbCliResponse());
  }

  function _getGeneratedAdminTemplateContent($table, $model_name, $template_path)
  {
    $constructor = new lmbAdminTemplatesConstructor($this->_getProjectConstructor(), $this->conn->getDatabaseInfo(), $table, $model_name);
    $constructor->create();

    return file_get_contents($this->dir_for_test_case . $template_path);
  }

  function _getExpectedAdminTemplateContent($vars, $template_name)
  {
    $view = lmbToolkit::instance()->createViewByTemplate($this->_admin_templates_path . 'admin_templates/' . $template_name . '.phtml');
    $view->setVariables($vars);

    $content = $view->render();
    $content = str_replace('[','{', $content);
    $content = str_replace(']','}', $content);

    return $content;
  }

  function testCreate_displayTemplate()
  {
    $table = $this->conn->getDatabaseInfo()->getTable('social_security');
    $model_name = 'SocialSecurity';
    $columns = $table->getColumns();

    $vars = array('model_name' => $model_name,
                  'columns' => $columns,
                  'fields_for_display' => array('id' => '[$item.id]',
                                                'code' => '[$item.code]'));

    $expected_content = $this->_getExpectedAdminTemplateContent($vars, 'display');
    $generated_content = $this->_getGeneratedAdminTemplateContent($table, $model_name, '/template/admin_social_security/display.phtml');

    $this->assertEqual($expected_content, $generated_content);
  }

  function testCreate_displayTemplateWithoutLazy()
  {
    $table = $this->conn->getDatabaseInfo()->getTable('document');
    $model_name = 'Document';
    $columns = $table->getColumns();

    //-- do not use `description` and `content` fields as lazy attributes
    $columns_for_display = array($table->getColumn('id'), $table->getColumn('title'), $table->getColumn('ctime'), $table->getColumn('utime'));

    $vars = array('model_name' => $model_name,
                  'columns' => $columns_for_display,
                  'fields_for_display' => array('id' => '[$item.id]',
                                                'title' => '[$item.title]',
                                                'ctime' => '[$item.ctime|date:"d.m.Y"]',
                                                'utime' => '[$item.utime|date:"d.m.Y"]'));

    $expected_content = $this->_getExpectedAdminTemplateContent($vars, 'display');
    $generated_content = $this->_getGeneratedAdminTemplateContent($table, $model_name, '/template/admin_document/display.phtml');

    $this->assertEqual($expected_content, $generated_content);
  }

  function testCreate_formFieldsTemplate()
  {
    $table = $this->conn->getDatabaseInfo()->getTable('social_security');
    $model_name = 'SocialSecurity';
    $columns = $table->getColumns();

    $vars = array('column_names' => array('code'),
                  'form_fields' => array('code' => '[[input id="code" name="code" type="text" title="code"/]]'));

    $expected_content = $this->_getExpectedAdminTemplateContent($vars, 'form_fields');
    $generated_content = $this->_getGeneratedAdminTemplateContent($table, $model_name, '/template/admin_social_security/include/form_fields.phtml');

    $this->assertEqual($expected_content, $generated_content);
  }

  function testCreate_formFieldsTemplateWithoutMetaFields()
  {
    $table = $this->conn->getDatabaseInfo()->getTable('document');
    $model_name = 'Document';
    $columns = $table->getColumns();

    $vars = array('column_names' => array('title', 'description', 'content'),
                  'form_fields' => array('title' => '[[input id="title" name="title" type="text" title="title"/]]',
                                         'description' => '[[textarea id="description" name="description" type="text" title="description" cols="40" rows="'.ceil($table->getColumn("description")->getSize()/40).'"/]]',
                                         'content' => '[[wysiwyg id="content" name="content" width="100%" height="300px" title="content"/]]',
                                         ));

    $expected_content = $this->_getExpectedAdminTemplateContent($vars, 'form_fields');
    $generated_content = $this->_getGeneratedAdminTemplateContent($table, $model_name, '/template/admin_document/include/form_fields.phtml');

    $this->assertEqual($expected_content, $generated_content);
  }

  function testCreate_formFieldsTemplateWithoutRelationFields()
  {
    $table = $this->conn->getDatabaseInfo()->getTable('person');
    $model_name = 'Person';
    $columns = $table->getColumns();

    $vars = array('column_names' => array('name'),
                  'form_fields' => array('name' => '[[input id="name" name="name" type="text" title="name"/]]'));

    $expected_content = $this->_getExpectedAdminTemplateContent($vars, 'form_fields');
    $generated_content = $this->_getGeneratedAdminTemplateContent($table, $model_name, '/template/admin_person/include/form_fields.phtml');

    $this->assertEqual($expected_content, $generated_content);
  }

  function testCreate_createTemplate()
  {
    $table = $this->conn->getDatabaseInfo()->getTable('social_security');

    $expected_content = $this->_getExpectedAdminTemplateContent(array('model_url' => 'social_security'), 'create');
    $generated_content = $this->_getGeneratedAdminTemplateContent($table, 'SocialSecurity', '/template/admin_social_security/create.phtml');

    $this->assertEqual($expected_content, $generated_content);
  }

  function testCreate_editTemplate()
  {
    $table = $this->conn->getDatabaseInfo()->getTable('social_security');

    $expected_content = $this->_getExpectedAdminTemplateContent(array('model_url' => 'social_security'), 'edit');
    $generated_content = $this->_getGeneratedAdminTemplateContent($table, 'SocialSecurity', '/template/admin_social_security/edit.phtml');

    $this->assertEqual($expected_content, $generated_content);
  }
}
