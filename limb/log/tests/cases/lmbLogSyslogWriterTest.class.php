<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/log/src/lmbLogSyslogWriter.class.php');

class lmbLogSyslogWriterTest extends UnitTestCase {

  function skip() {
      $log_exists = file_exists('/var/log/syslog');
      $this->skipIf(!$log_exists, 'Syslog writer test skipped, because /var/log/syslog not found');
  }

  function testWrite()
  {
    $writer = new lmbLogSyslogWriter(new lmbUri());
    $writer->write(new lmbLogEntry(LOG_ERR, "foo\nbar"));
    $content = file_get_contents('/var/log/syslog');
    $this->assertPattern('/Error/', $content);
    $this->assertPattern('/foo/', $content);
  }
}
