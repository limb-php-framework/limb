<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbLogTest.class.php 5009 2007-02-08 15:37:31Z pachanga $
 * @package    util
 */
lmb_require('limb/util/src/util/lmbLog.class.php');

class lmbLogTest extends UnitTestCase
{
  function setUp()
  {
    if(file_exists(LIMB_VAR_DIR . '/tmp/test.log'))
      unlink(LIMB_VAR_DIR . '/tmp/test.log');
  }

  function tearDown()
  {
    clearstatcache();
    if(file_exists(LIMB_VAR_DIR . '/tmp/test.log'))
      unlink(LIMB_VAR_DIR . '/tmp/test.log');
  }

  function testWritingToFile()
  {
    lmbLog :: write(LIMB_VAR_DIR . '/tmp/test.log', 'wow');

    $this->assertWantedPattern('|wow|', file_get_contents(LIMB_VAR_DIR . '/tmp/test.log'));
  }

  function testLogRotate()
  {
    lmbLog :: write(LIMB_VAR_DIR . '/tmp/test.log', 'wow');

    lmbLog :: rotate(LIMB_VAR_DIR . '/tmp/test.log', 2);
    $this->assertWantedPattern('|wow|', file_get_contents(LIMB_VAR_DIR . '/tmp/test.log.1'));

    lmbLog :: write(LIMB_VAR_DIR . '/tmp/test.log', 'hey');

    lmbLog :: rotate(LIMB_VAR_DIR . '/tmp/test.log', 2);
    $this->assertWantedPattern('|hey|', file_get_contents(LIMB_VAR_DIR . '/tmp/test.log.1'));
    $this->assertWantedPattern('|wow|', file_get_contents(LIMB_VAR_DIR . '/tmp/test.log.2'));

    lmbLog :: write(LIMB_VAR_DIR . '/tmp/test.log', 'bla');

    lmbLog :: rotate(LIMB_VAR_DIR . '/tmp/test.log', 2);
    $this->assertWantedPattern('|bla|', file_get_contents(LIMB_VAR_DIR . '/tmp/test.log.1'));
    $this->assertWantedPattern('|hey|', file_get_contents(LIMB_VAR_DIR . '/tmp/test.log.2'));
    $this->assertFalse(file_exists(LIMB_VAR_DIR . '/tmp/test.log.3'));

  }
}

?>