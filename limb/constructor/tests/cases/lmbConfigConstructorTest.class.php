<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/constructor/tests/cases/lmbConstructorUnitTestCase.class.php');
lmb_require('limb/constructor/src/lmbConfigConstructor.class.php');
lmb_require('limb/cli/src/lmbCliResponse.class.php');

class lmbConfigConstructorTest extends lmbConstructorUnitTestCase
{
  protected $_templates_path;

  function __construct()
  {
    parent :: __construct();

    $this->_templates_path = dirname(__FILE__) . '/../../template/';
  }

  function _getProjectConstructor()
  {
    return new lmbProjectConstructor($this->dir_for_test_case, new lmbCliResponse());
  }

  function _getGeneratedConfigContent($table, $template_path)
  {
    $constructor = new lmbConfigConstructor($this->_getProjectConstructor(), $this->conn->getDatabaseInfo(), $table);
    $constructor->create();

    return file_get_contents($this->dir_for_test_case . $template_path);
  }

  function _getExpectedConfigContent($vars, $template_name, $tags_needed = true)
  {
    $view = lmbToolkit::instance()->createViewByTemplate($this->_templates_path . 'settings/' . $template_name . '.phtml');

    $view->setVariables($vars);
    $content = $view->render();

    if($tags_needed)
      $content = '<?php'. PHP_EOL . $content;

    return $content;
  }

  function testCreateEmptyConfig()
  {
    $table = $this->conn->getDatabaseInfo()->getTable('social_security');

    $vars = array('table_name' => $table->getName());

    $expected_content = $this->_getExpectedConfigContent($vars, 'model_config');
    $generated_content = $this->_getGeneratedConfigContent($table, '/settings/social_security.conf.php');

    $this->assertEqual($expected_content, $generated_content);
  }
}
