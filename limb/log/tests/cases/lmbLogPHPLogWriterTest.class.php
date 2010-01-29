<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/log/src/lmbLogPHPLogWriter.class.php');

class lmbLogPHPLogWriterTest extends UnitTestCase {

  function testWrite()
  {
    $php_log = lmb_var_dir().'/php.log';
    ini_set('error_log', $php_log);

    $writer = new lmbLogPHPLogWriter(new lmbUri());
    $writer->write(new lmbLogEntry(LOG_ERR, 'foo'));

    $content = file_get_contents($php_log);
    $this->assertPattern('/Error/', $content);
    $this->assertPattern('/foo/', $content);
  }
}
