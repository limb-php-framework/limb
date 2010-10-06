<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/log/src/writers/lmbLogFileWriter.class.php');
lmb_require('limb/net/src/lmbUri.class.php');

class lmbLogFileWriterTest extends UnitTestCase
{
  function testWrite()
  {
    $dsn = new lmbUri('file://'.lmb_var_dir().'/logs/error'.uniqid().'.log');
    $writer = new lmbLogFileWriter($dsn);

    $entry = new lmbLogEntry(LOG_ERR, 'foo');
    $writer->write($entry);

    $content = file_get_contents($writer->getLogFile());
    $this->assertPattern('/Error/', $content);
    $this->assertPattern('/foo/', $content);
  }

  function testWrite_SetEntryTitle()
  {
    $toolkit = lmbToolkit::instance();
    $toolkit->setLogEntryTitle('some_title');

    $dsn = new lmbUri('file://'.lmb_var_dir().'/logs/info'.uniqid().'.log');

    $conf = array(
      'logs' => array('foo' => $dsn)
    );

    $toolkit->setConf('log', $conf);
    $toolkit->log('message', $level = LOG_INFO, $params = array(), null, $log_name = 'foo');

    $writers = $toolkit->getLog('foo')->getWriters();
    $this->assertIsA($writers[0], 'lmbLogFileWriter');

    $content = file_get_contents($writers[0]->getLogFile());

    $this->assertPattern('/some_title/', $content);
    $this->assertPattern('/Info/', $content);
    $this->assertPattern('/message/', $content);
  }
}
