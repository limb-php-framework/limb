<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/log/src/lmbLogEchoWriter.class.php');

class lmbLogEchoWriterTest extends UnitTestCase {

  function testWrite()
  {
    $writer = new lmbLogEchoWriter(new lmbUri());
    ob_start();
    $writer->write(new lmbLogEntry(LOG_ERR, 'foo'));
    $output = ob_get_contents();
    ob_end_clean();
    $this->assertPattern('/Error/', $output);
    $this->assertPattern('/foo/', $output);
  }
}
