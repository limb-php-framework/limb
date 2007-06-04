<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTestGroupTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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
