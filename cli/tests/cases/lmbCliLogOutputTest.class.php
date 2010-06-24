<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/cli/src/lmbCliLogOutput.class.php');
lmb_require('limb/log/src/lmbLog.class.php');
lmb_require('limb/log/src/writers/lmbLogMemoryWriter.class.php');

class lmbCliLogOutputTest extends UnitTestCase
{
  function testWrite()
  {
    $log = new lmbLog;
    $log->registerWriter($writer = new lmbLogMemoryWriter(new lmbUri()));

    $out = new lmbCliLogOutput($log);
    $out->write('message', array('param1' => 'value1'));

    $entry = current($writer->getEntries());
    $this->assertEqual('message', $entry->getMessage());
    $this->assertEqual(array('param1' => 'value1'), $entry->getParams());
    $this->assertEqual(LOG_INFO, $entry->getLevel());
  }

  function testError()
  {
    $log = new lmbLog;
    $log->registerWriter($writer = new lmbLogMemoryWriter(new lmbUri()));

    $out = new lmbCliLogOutput($log);
    $out->error('error', array('param2' => 'value2'), LOG_NOTICE);

    $entry = current($writer->getEntries());
    $this->assertEqual('error', $entry->getMessage());
    $this->assertEqual(array('param2' => 'value2'), $entry->getParams());
    $this->assertEqual(LOG_NOTICE, $entry->getLevel());
  }
}

