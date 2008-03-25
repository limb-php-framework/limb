<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

class lmbMailerTest extends UnitTestCase {

  function testConfiguration()
  {
    define('LIMB_SMTP_HOST', 'foo');

    define('LIMB_SMTP_PORT', 'bar');
    $config = array('smtp_port' => 'baz');

    require_once(dirname(__FILE__).'/../../src/lmbMailer.class.php');

    $mailer = new lmbMailer($config);
    $this->assertEqual($mailer->smtp_host, 'foo');
    $this->assertEqual($mailer->smtp_port, 'baz');
  }

  function testManualConfiguration()
  {
    require_once(dirname(__FILE__).'/../../src/lmbMailer.class.php');

    $mailer = new lmbMailer();

    $mailer->smtp_host = 'foo';

    $config = array('smtp_port' => 'baz');
    $mailer->setConfig($config);

    $this->assertEqual($mailer->smtp_host, 'foo');
    $this->assertEqual($mailer->smtp_port, 'baz');
  }

}
?>
