<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__) . '/../common.inc.php');
require_once(dirname(__FILE__) . '/../../src/lmbTestTreeFileNode.class.php');

class lmbTestTreeFileNodeTest extends lmbTestRunnerBase
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
    "<?php\n" . $foo->generateClass() . "\n" . $bar->generateClass() . "\n?>");

    $node = new lmbTestTreeFileNode(LIMB_VAR_DIR . '/module.php');

    ob_start();
    $group = $node->createTestGroup();

    $group->run(new SimpleReporter());
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, $foo->getOutput() . $bar->getOutput());
  }

  function testCreateTestGroupUsingClass()
  {
    $foo = new GeneratedTestClass();
    $bar = new GeneratedTestClass();
    //module must be unique across test cases since require_once is used
    file_put_contents(LIMB_VAR_DIR . '/unique_module_name.php',
    "<?php\n" . $foo->generateClass() . "\n" . $bar->generateClass() . "\n?>");

    $node = new lmbTestTreeFileNode(LIMB_VAR_DIR . '/unique_module_name.php', $foo->getClass());

    ob_start();
    $group = $node->createTestGroup();

    $group->run(new SimpleReporter());
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, $foo->getOutput());
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
