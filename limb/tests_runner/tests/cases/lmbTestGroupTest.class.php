<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestGroupTest.class.php 5006 2007-02-08 15:37:13Z pachanga $
 * @package    tests_runner
 */
require_once(dirname(__FILE__) . '/../common.inc.php');
require_once(dirname(__FILE__) . '/../../src/lmbTestGroup.class.php');
require_once(dirname(__FILE__) . '/../../src/lmbDetachedFixture.class.php');

Mock :: generate('lmbDetachedFixture', 'MockDetachedFixture');

class lmbTestGroupTest extends lmbTestsUtilitiesBase
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

  function testUseFixture()
  {
    $fixture = new MockDetachedFixture();

    $fixture->expectOnce('setUp');
    $fixture->expectOnce('tearDown');

    $group = new lmbTestGroup(LIMB_VAR_DIR);
    $group->useFixture($fixture);

    ob_start();
    $group->run(new SimpleReporter());
    ob_end_clean();
  }
}

?>
