<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestTreeFileNodeTest.class.php 5006 2007-02-08 15:37:13Z pachanga $
 * @package    tests_runner
 */
require_once(dirname(__FILE__) . '/../common.inc.php');
require_once(dirname(__FILE__) . '/../../src/lmbTestTreeFileNode.class.php');

class lmbTestTreeFileNodeTest extends lmbTestsUtilitiesBase
{
  function setUp()
  {
    $this->_rmdir(LIMB_VAR_DIR);
    mkdir(LIMB_VAR_DIR);
  }

  function tearDown()
  {
    $this->_rmdir(LIMB_VAR_DIR);
  }

  function testCreateTestGroupUsingFileModule()
  {
    $foo = new GeneratedTestClass();
    $bar = new GeneratedTestClass();
    file_put_contents(LIMB_VAR_DIR . '/module.php',
    "<?php\n" . $foo->generateBareBoned() . "\n" . $bar->generateBareBoned() . "\n?>");

    $node = new lmbTestTreeFileNode(LIMB_VAR_DIR . '/module.php');

    ob_start();
    $group = $node->createTestGroup();

    $group->run(new SimpleReporter());
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, $foo->getClass() . $bar->getClass());
  }

  function testCreateTestGroupUsingClass()
  {
    $foo = new GeneratedTestClass();
    $bar = new GeneratedTestClass();
    //module must be unique across test cases since require_once is used
    file_put_contents(LIMB_VAR_DIR . '/unique_module_name.php',
    "<?php\n" . $foo->generateBareBoned() . "\n" . $bar->generateBareBoned() . "\n?>");

    $node = new lmbTestTreeFileNode(LIMB_VAR_DIR . '/unique_module_name.php', $foo->getClass());

    ob_start();
    $group = $node->createTestGroup();

    $group->run(new SimpleReporter());
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, $foo->getClass());
  }

  function testGetTestLabel()
  {
    $foo = new GeneratedTestClass();
    file_put_contents(LIMB_VAR_DIR . '/foo.php', $foo->generate());

    $node = new lmbTestTreeFileNode(LIMB_VAR_DIR . '/foo.php');
    $this->assertEqual($node->getTestLabel(), 'foo.php');
    $group = $node->createTestGroup();
    $this->assertEqual($group->getLabel(), 'foo.php');
  }
}

?>
