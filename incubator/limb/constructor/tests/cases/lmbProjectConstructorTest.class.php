<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/constructor/tests/cases/lmbConstructorUnitTestCase.class.php');
lmb_require('limb/constructor/src/lmbProjectConstructor.class.php');
lmb_require('limb/cli/src/lmbCliResponse.class.php');

class lmbProjectConstructorTest extends lmbConstructorUnitTestCase
{
  /**
   * @var lmbProject
   */
  protected $project;

  function setUp()
  {
    parent::setUp();
    $this->project = new lmbProjectConstructor($this->dir_for_test_case, new lmbCliResponse());
  }

  function testAddTemplate()
  {
    $this->project->addTemplate('main_page/foo.phtml', $content = 'bar');
    $result_path = $this->dir_for_test_case.'/template/main_page/foo.phtml';
    if($this->assertTrue(file_exists($result_path)))
      $this->assertEqual(file_get_contents($result_path), $content);
  }

  function testAddController()
  {
    $controller_name = 'FooController.class.php';
    $content = 'bar';
    $this->project->addController($controller_name, $content);
    $result_path = $this->dir_for_test_case.'/src/controller/'.$controller_name;
    if($this->assertTrue(file_exists($result_path)))
      $this->assertEqual(file_get_contents($result_path), $content);
  }

  function testAddModel()
  {
    $model_name = 'Foo.class.php';
    $content = 'bar';
    $this->project->addModel($model_name, $content);
    $result_path = $this->dir_for_test_case.'/src/model/'.$model_name;
    if($this->assertTrue(file_exists($result_path)))
      $this->assertEqual(file_get_contents($result_path), $content);
  }
}