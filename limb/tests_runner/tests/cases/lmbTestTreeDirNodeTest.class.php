<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestTreeDirNodeTest.class.php 5569 2007-04-09 08:39:30Z pachanga $
 * @package    tests_runner
 */
require_once(dirname(__FILE__) . '/../common.inc.php');
require_once(dirname(__FILE__) . '/../../src/lmbTestTreeDirNode.class.php');
require_once(dirname(__FILE__) . '/../../src/lmbTestFileFilter.class.php');

class lmbTestTreeDirNodeTest extends lmbTestsUtilitiesBase
{
  protected $var_dir;

  function setUp()
  {
    $this->_rmdir(LIMB_VAR_DIR);
    //we need unique temporary dir since test modules are included once
    $this->var_dir = LIMB_VAR_DIR . '/' . mt_rand();
    mkdir(LIMB_VAR_DIR);
    mkdir($this->var_dir);
  }

  function tearDown()
  {
    $this->_rmdir(LIMB_VAR_DIR);
  }

  function testGetChildren()
  {
    mkdir($this->var_dir . '/a');
    mkdir($this->var_dir . '/a/b');

    touch($this->var_dir . '/a/b/bar_test.php');
    touch($this->var_dir . '/a/b/foo_test.php');

    $node = new lmbTestTreeDirNode($this->var_dir);
    $child_nodes = $node->getChildren();
    $this->assertEqual(sizeof($child_nodes), 1);
    $this->assertEqual($child_nodes[0]->getDir(), $this->var_dir . '/a');
    $this->assertFalse($child_nodes[0]->isTerminal());

    $sub_child_nodes = $child_nodes[0]->getChildren();
    $this->assertEqual(sizeof($sub_child_nodes), 1);
    $this->assertEqual($sub_child_nodes[0]->getDir(), $this->var_dir . '/a/b');
    $this->assertFalse($sub_child_nodes[0]->isTerminal());

    $terminal_nodes = $sub_child_nodes[0]->getChildren();

    $this->assertTrue($terminal_nodes[0]->getFile(), $this->var_dir . '/a/b/bar_test.php');
    $this->assertTrue($terminal_nodes[0]->isTerminal());
    $this->assertTrue($terminal_nodes[1]->getFile(), $this->var_dir . '/a/b/foo_test.php');
    $this->assertTrue($terminal_nodes[1]->isTerminal());
  }

  function testUseFileFilter()
  {
    touch($this->var_dir . '/bar_test.php');
    touch($this->var_dir . '/bah.php');
    touch($this->var_dir . '/junk.php');
    touch($this->var_dir . '/FooYo.class.php');

    $node = new lmbTestTreeDirNode($this->var_dir, array('*test.php', '*Yo.class.php'));
    $nodes = $node->getChildren();
    $this->assertEqual(sizeof($nodes), 2);
    $this->assertEqual($nodes[0]->getFile(), $this->var_dir . '/FooYo.class.php');
    $this->assertEqual($nodes[1]->getFile(), $this->var_dir . '/bar_test.php');
  }

  function testUseFileFilterAndClassFormat()
  {
    $foo = new GeneratedTestClass();
    touch($this->var_dir . '/junk.php');
    touch($this->var_dir . '/' . $foo->getClass() . '.class.php');

    $node = new lmbTestTreeDirNode($this->var_dir, array('*.class.php'), '%s.class.php');
    $nodes = $node->getChildren();
    $this->assertEqual(sizeof($nodes), 1);
    $this->assertEqual($nodes[0]->getFile(), $this->var_dir . '/' . $foo->getClass() . '.class.php');
    $this->assertEqual($nodes[0]->getClass(), $foo->getClass());
  }

  function testFileFilterIsInherited()
  {
    mkdir($this->var_dir . '/a');
    touch($this->var_dir . '/a/BarTest.class.php');
    touch($this->var_dir . '/a/garbage.php');
    touch($this->var_dir . '/bar_test.php');
    touch($this->var_dir . '/bah.php');
    touch($this->var_dir . '/junk.php');
    touch($this->var_dir . '/FooTest.class.php');

    $node = new lmbTestTreeDirNode($this->var_dir, array('*test.php', '*Test.class.php'));
    $nodes = $node->getChildren();
    $this->assertEqual(sizeof($nodes), 3);

    $this->assertEqual($nodes[0]->getDir(), $this->var_dir . '/a');

    $a_nodes = $nodes[0]->getChildren();
    $this->assertEqual(sizeof($a_nodes), 1);
    $this->assertEqual($a_nodes[0]->getFile(), $this->var_dir . '/a/BarTest.class.php');

    $this->assertEqual($nodes[1]->getFile(), $this->var_dir . '/FooTest.class.php');
    $this->assertEqual($nodes[2]->getFile(), $this->var_dir . '/bar_test.php');
  }

  function testFileFilterAndClassFormatAreInherited()
  {
    mkdir($this->var_dir . '/a');
    touch($this->var_dir . '/a/BarTest.klass.php');
    touch($this->var_dir . '/a/garbage.php');
    touch($this->var_dir . '/bar_test.php');
    touch($this->var_dir . '/bah.php');
    touch($this->var_dir . '/junk.php');
    touch($this->var_dir . '/FooTest.klass.php');

    $node = new lmbTestTreeDirNode($this->var_dir, array('*test.php', '*Test.klass.php'), '%s.klass.php');
    $nodes = $node->getChildren();
    $this->assertEqual(sizeof($nodes), 3);

    $this->assertEqual($nodes[0]->getDir(), $this->var_dir . '/a');

    $a_nodes = $nodes[0]->getChildren();
    $this->assertEqual(sizeof($a_nodes), 1);
    $this->assertEqual($a_nodes[0]->getFile(), $this->var_dir . '/a/BarTest.klass.php');
    $this->assertEqual($a_nodes[0]->getClass(), 'BarTest');

    $this->assertEqual($nodes[1]->getFile(), $this->var_dir . '/FooTest.klass.php');
    $this->assertEqual($nodes[1]->getClass(), 'FooTest');
    $this->assertEqual($nodes[2]->getFile(), $this->var_dir . '/bar_test.php');
  }

  function testFindChildByPath()
  {
    mkdir($this->var_dir . '/a');

    touch($this->var_dir . '/a/bar_test.php');
    touch($this->var_dir . '/a/foo_test.php');

    $node = new lmbTestTreeDirNode($this->var_dir);
    $child_node = $node->findChildByPath('/0/1');
    $this->assertTrue($child_node->isTerminal());
    $this->assertEqual($child_node->getFile(), $this->var_dir . '/a/foo_test.php');
  }

  function testFindNonTerminalGroupByPath()
  {
    mkdir($this->var_dir . '/a');
    mkdir($this->var_dir . '/a/b');

    touch($this->var_dir . '/a/b/bar_test.php');
    touch($this->var_dir . '/a/b/foo_test.php');

    $node = new lmbTestTreeDirNode($this->var_dir);
    $child_node = $node->findChildByPath('/0/0');
    $this->assertFalse($child_node->isTerminal());
    $this->assertEqual($child_node->getDir(), $this->var_dir . '/a/b');
  }

  function testFindChildByOnlySlashPath()
  {
    $node = new lmbTestTreeDirNode($this->var_dir);
    $child_node = $node->findChildByPath('/');
    $this->assertEqual($child_node, $node);
  }

  function testCreateTestGroup()
  {
    mkdir($this->var_dir . '/a');

    $test1 = new GeneratedTestClass();
    $test2 = new GeneratedTestClass();

    file_put_contents($this->var_dir . '/a/.setup.php', '<?php echo "wow"; ?>');
    file_put_contents($this->var_dir . '/a/.teardown.php', '<?php echo "hey"; ?>');

    file_put_contents($this->var_dir . '/a/bar_test.php', $test1->generate());
    file_put_contents($this->var_dir . '/a/foo_test.php', $test2->generate());

    $node = new lmbTestTreeDirNode($this->var_dir);

    //we check for any possible garbage during php includes
    ob_start();
    $group = $node->createTestGroup();

    $group->run(new SimpleReporter());
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, "wow" . $test1->getClass() . $test2->getClass() . "hey");
  }

  function testCreateTestGroupWithParentsForTerminalNode()
  {
    mkdir($this->var_dir . '/a');
    mkdir($this->var_dir . '/a/b');

    $test1 = new GeneratedTestClass();
    $test2 = new GeneratedTestClass();

    file_put_contents($this->var_dir . '/a/.setup.php', '<?php echo "wow"; ?>');
    file_put_contents($this->var_dir . '/a/.teardown.php', '<?php echo "hey"; ?>');

    file_put_contents($this->var_dir . '/a/b/bar_test.php', $test1->generate());
    file_put_contents($this->var_dir . '/a/b/foo_test.php', $test2->generate());

    $root_node = new lmbTestTreeDirNode($this->var_dir);
    $terminal_node = $root_node->findChildByPath('/0/0/1');

    //we check for any possible garbage during php includes
    ob_start();
    $group = $terminal_node->createTestGroupWithParents();

    $group->run(new SimpleReporter());
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, "wow" . $test2->getClass() . "hey");
  }

  function testCreateTestGroupWithParentsForTopLevelTerminalNode()
  {
    mkdir($this->var_dir . '/a');

    $test = new GeneratedTestClass();

    file_put_contents($this->var_dir . '/a/.setup.php', '<?php echo "wow"; ?>');
    file_put_contents($this->var_dir . '/a/.teardown.php', '<?php echo "hey"; ?>');

    file_put_contents($this->var_dir . '/a/bar_test.php', $test->generate());

    $root_node = new lmbTestTreeDirNode($this->var_dir . '/a');
    $terminal_node = $root_node->findChildByPath('/0');

    //we check for any possible garbage during php includes
    ob_start();
    $group = $terminal_node->createTestGroupWithParents();

    $group->run(new SimpleReporter());
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, "wow" . $test->getClass() . "hey");
  }

  function testCreateTestGroupWithParents()
  {
    mkdir($this->var_dir . '/a');
    mkdir($this->var_dir . '/a/b');

    $test1 = new GeneratedTestClass();
    $test2 = new GeneratedTestClass();

    $skipped_test1 = new GeneratedTestClass();
    $skipped_test2 = new GeneratedTestClass();

    file_put_contents($this->var_dir . '/a/.setup.php', '<?php echo "|wow_start|"; ?>');
    file_put_contents($this->var_dir . '/a/.teardown.php', '<?php echo "|wow_end|"; ?>');

    file_put_contents($this->var_dir . '/a/skipped1_test.php', $skipped_test1->generate());
    file_put_contents($this->var_dir . '/a/skipped2_test.php', $skipped_test2->generate());

    file_put_contents($this->var_dir . '/a/b/.setup.php', '<?php echo "|hey_start|"; ?>');
    file_put_contents($this->var_dir . '/a/b/.teardown.php', '<?php echo "|hey_end|"; ?>');

    file_put_contents($this->var_dir . '/a/b/bar_test.php', $test1->generate());
    file_put_contents($this->var_dir . '/a/b/foo_test.php', $test2->generate());

    $root_node = new lmbTestTreeDirNode($this->var_dir);

    $b_node = $root_node->findChildByPath('/0/0');
    $this->assertFalse($b_node->isTerminal());
    $this->assertEqual($b_node->getDir(), $this->var_dir . '/a/b');

    //we check for any possible garbage during php includes
    ob_start();
    $group = $b_node->createTestGroupWithParents();

    $group->run(new SimpleReporter());
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, "|wow_start|" . "|hey_start|" .
                             $test1->getClass() . $test2->getClass() .
                             "|hey_end|" . "|wow_end|");

  }

  function testGetTestLabel()
  {
    file_put_contents($this->var_dir . '/.description', 'Foo');

    $node = new lmbTestTreeDirNode($this->var_dir);
    $this->assertEqual($node->getTestLabel(), 'Foo');
    $group = $node->createTestGroup();
    $this->assertEqual($group->getLabel(), 'Foo');
  }

  function testGetDefaultTestLabel()
  {
    $node = new lmbTestTreeDirNode($this->var_dir);
    $this->assertEqual($node->getTestLabel(), 'Group test in "' . $this->var_dir . '"');
    $group = $node->createTestGroup();
    $this->assertEqual($group->getLabel(), 'Group test in "' . $this->var_dir . '"');
  }

  function testBootstrap()
  {
    file_put_contents($this->var_dir . '/.init.php', '<?php echo "hey!"; ?>');

    $node = new lmbTestTreeDirNode($this->var_dir);
    ob_start();
    $group = $node->bootstrap();
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, "hey!");
  }

  function testBootstrapPath()
  {
    mkdir($this->var_dir . '/a');
    mkdir($this->var_dir . '/a/b');

    $test1 = new GeneratedTestClass();

    file_put_contents($this->var_dir . '/a/.init.php', '<?php echo "wow"; ?>');

    file_put_contents($this->var_dir . '/a/b/bar_test.php', $test1->generate());

    $root_node = new lmbTestTreeDirNode($this->var_dir);

    ob_start();
    $root_node->bootstrapPath('/0/0/0');
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, "wow");

    $terminal_node = $root_node->findChildByPath('/0/0/0');

    ob_start();
    //we check for any possible garbage during php includes
    $group = $terminal_node->createTestGroupWithParents();

    $group->run(new SimpleReporter());
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, $test1->getClass());
  }

  function testBootstrapForChildDirectories()
  {
    mkdir($this->var_dir . '/a');
    mkdir($this->var_dir . '/a/b');
    mkdir($this->var_dir . '/a/c');

    $test1 = new GeneratedTestClass();

    file_put_contents($this->var_dir . '/a/b/bar_test.php', $test1->generate());
    file_put_contents($this->var_dir . '/a/c/.init.php', '<?php echo "wow"; ?>');

    $root_node = new lmbTestTreeDirNode($this->var_dir);

    $node = $root_node->findChildByPath('/0');

    ob_start();
    //we check for any possible garbage during php includes
    $group = $node->createTestGroupWithParents();

    $group->run(new SimpleReporter());
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, 'wow' . $test1->getClass());
  }

  function testIgnoreTestsDirectory()
  {
    mkdir($this->var_dir . '/a');
    mkdir($this->var_dir . '/a/b');

    $test1 = new GeneratedTestClass();
    $test2 = new GeneratedTestClass();

    file_put_contents($this->var_dir . '/a/bar_test.php', $test1->generate());
    file_put_contents($this->var_dir . '/a/b/foo_test.php', $test2->generate());

    touch($this->var_dir . '/a/b/.ignore');

    $root_node = new lmbTestTreeDirNode($this->var_dir);
    $group = $root_node->createTestGroup();

    ob_start();
    $group->run(new SimpleReporter());
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, $test1->getClass());
  }

  function testIgnoreConditionalTrueTestsDirectory()
  {
    mkdir($this->var_dir . '/a');
    mkdir($this->var_dir . '/a/b');

    $test1 = new GeneratedTestClass();
    $test2 = new GeneratedTestClass();

    file_put_contents($this->var_dir . '/a/bar_test.php', $test1->generate());
    file_put_contents($this->var_dir . '/a/b/foo_test.php', $test2->generate());

    file_put_contents($this->var_dir . '/a/b/.ignore.php', '<?php return true; ?>');

    $root_node = new lmbTestTreeDirNode($this->var_dir);
    $group = $root_node->createTestGroup();

    ob_start();
    $group->run(new SimpleReporter());
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, $test1->getClass());
  }

  function testIgnoreConditionalFalseTestsDirectory()
  {
    mkdir($this->var_dir . '/a');
    mkdir($this->var_dir . '/a/b');

    $test1 = new GeneratedTestClass();
    $test2 = new GeneratedTestClass();

    file_put_contents($this->var_dir . '/a/bar_test.php', $test1->generate());
    file_put_contents($this->var_dir . '/a/b/foo_test.php', $test2->generate());

    file_put_contents($this->var_dir . '/a/b/.ignore.php', '<?php return false; ?>');

    $root_node = new lmbTestTreeDirNode($this->var_dir);
    $group = $root_node->createTestGroup();

    ob_start();
    $group->run(new SimpleReporter());
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, $test2->getClass() . $test1->getClass());
  }

  function testIgnoredDirFixtureIgnoredToo()
  {
    mkdir($this->var_dir . '/a');
    $test = new GeneratedTestClass();

    file_put_contents($this->var_dir . '/a/.setup.php', '<?php echo "No!" ?>');
    file_put_contents($this->var_dir . '/a/bar_test.php', $test->generate());

    file_put_contents($this->var_dir . '/a/.ignore.php', '<?php return true; ?>');

    $root_node = new lmbTestTreeDirNode($this->var_dir);
    $group = $root_node->createTestGroup();

    ob_start();
    $group->run(new SimpleReporter());
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, '');
  }

  function testBootstrappingDoesntHappenIfDirIsIgnored()
  {
    mkdir($this->var_dir . '/a');
    mkdir($this->var_dir . '/a/b');

    $test1 = new GeneratedTestClass();
    $test2 = new GeneratedTestClass();

    file_put_contents($this->var_dir . '/a/bar_test.php', $test1->generate());
    file_put_contents($this->var_dir . '/a/b/foo_test.php', $test2->generate());

    file_put_contents($this->var_dir . '/a/b/.init.php', '<?php echo "wow" ?>');
    file_put_contents($this->var_dir . '/a/b/.ignore.php', '<?php return true; ?>');

    $root_node = new lmbTestTreeDirNode($this->var_dir);
    $group = $root_node->createTestGroup();

    ob_start();
    $group->run(new SimpleReporter());
    $str = ob_get_contents();
    ob_end_clean();
    $this->assertEqual($str, $test1->getClass());
  }
}

?>
