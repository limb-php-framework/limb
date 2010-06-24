<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/log/src/lmbLogEntry.class.php');

class lmbLogEntryTest extends UnitTestCase {

  function testGetters()
  {
    $entry = new lmbLogEntry(
      $level = LOG_INFO,
      $message = 'some text',
      $params = array('foo' => 42),
      $backtrace = new lmbBacktrace(),
      $time = time()
    );
    $this->assertEqual($level, $entry->getLevel());
    $this->assertEqual($message, $entry->getMessage());
    $this->assertEqual($params, $entry->getParams());
    $this->assertEqual($backtrace, $entry->getBacktrace());
    $this->assertEqual($time, $entry->getTime());
  }

  function testGetLevelForHuman()
  {
    $entry = new lmbLogEntry(LOG_ERR,'foo');
    $this->assertEqual('Error', $entry->getLevelForHuman());
  }

  function testIsLevel()
  {
    $entry = new lmbLogEntry(LOG_ERR,'foo');
    $this->assertTrue($entry->isLevel(LOG_ERR));
    $this->assertFalse($entry->isLevel(LOG_INFO));
  }

  function testAsText()
  {
    $entry = new lmbLogEntry(LOG_ERR,'foo&');
    $this->assertPattern('/Error/', $entry->asText());
    $this->assertPattern('/foo&/', $entry->asText());
  }

  function testAsHtml()
  {
    $entry = new lmbLogEntry(LOG_ERR,'foo&');
    $this->assertPattern('/Error/', $entry->asHtml());
    $this->assertPattern('/foo&amp;/', $entry->asHtml());
  }
}