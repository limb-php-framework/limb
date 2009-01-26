<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once(dirname(__FILE__).'/../../src/lmbMailer.class.php');

class lmbMailerTest extends UnitTestCase {

  function testConstructorConfiguration()
  {
    $config = array('smtp_port' => 'baz');

    $mailer = new lmbMailer($config);
    $this->assertEqual($mailer->smtp_host, LIMB_SMTP_HOST);
    $this->assertEqual($mailer->smtp_port, 'baz');
    $this->assertNotEqual('baz', LIMB_SMTP_PORT);
  }

  function testSetConfig()
  {
    $mailer = new lmbMailer();

    $mailer->smtp_host = 'foo';

    $config = array('smtp_port' => 'baz');
    $mailer->setConfig($config);

    $this->assertEqual($mailer->smtp_host, 'foo');
    $this->assertEqual($mailer->smtp_port, 'baz');
  }

  function testProcessMailRecepients()
  {
    $mailer = new lmbMailer();
    
    $recs = $mailer->processMailRecipients("bob@localhost");
    $this->assertEqual(sizeof($recs), 1);
    $this->assertEqual($recs[0]['address'], "bob@localhost");
    $this->assertEqual($recs[0]['name'], "");

    $recs = $mailer->processMailRecipients("Bob<bob@localhost>");
    $this->assertEqual(sizeof($recs), 1);
    $this->assertEqual($recs[0]['address'], "bob@localhost");
    $this->assertEqual($recs[0]['name'], "Bob");

    $recs = $mailer->processMailRecipients(array("bob@localhost"));
    $this->assertEqual(sizeof($recs), 1);
    $this->assertEqual($recs[0]['address'], "bob@localhost");
    $this->assertEqual($recs[0]['name'], "");

    $recs = $mailer->processMailRecipients(array("name" => "Bob", "address" => "bob@localhost"));
    $this->assertEqual(sizeof($recs), 1);
    $this->assertEqual($recs[0]['address'], "bob@localhost");
    $this->assertEqual($recs[0]['name'], "Bob");

    $recs = $mailer->processMailRecipients(array("Bob<bob@localhost>"));
    $this->assertEqual(sizeof($recs), 1);
    $this->assertEqual($recs[0]['address'], "bob@localhost");
    $this->assertEqual($recs[0]['name'], "Bob");

    $recs = $mailer->processMailRecipients(array("bob@localhost", "todd@localhost"));
    $this->assertEqual(sizeof($recs), 2);
    $this->assertEqual($recs[0]['address'], "bob@localhost");
    $this->assertEqual($recs[0]['name'], "");
    $this->assertEqual($recs[1]['address'], "todd@localhost");
    $this->assertEqual($recs[1]['name'], "");

    $recs = $mailer->processMailRecipients(array("Bob<bob@localhost>", "todd@localhost"));
    $this->assertEqual(sizeof($recs), 2);
    $this->assertEqual($recs[0]['address'], "bob@localhost");
    $this->assertEqual($recs[0]['name'], "Bob");
    $this->assertEqual($recs[1]['address'], "todd@localhost");
    $this->assertEqual($recs[1]['name'], "");

    $recs = $mailer->processMailRecipients(array(array("name" => "Bob", "address" => "bob@localhost"), "todd@localhost"));
    $this->assertEqual(sizeof($recs), 2);
    $this->assertEqual($recs[0]['address'], "bob@localhost");
    $this->assertEqual($recs[0]['name'], "Bob");
    $this->assertEqual($recs[1]['address'], "todd@localhost");
    $this->assertEqual($recs[1]['name'], "");
  }
}
