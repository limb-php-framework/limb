<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbWebAppTestCase.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */

lmb_require('limb/dbal/src/lmbSimpleDb.class.php');

class lmbWebAppTestCase extends UnitTestCase
{
  protected $toolkit;
  protected $request;
  protected $response;
  protected $db;
  protected $connection;

  function setUp()
  {
    $this->toolkit = lmbToolkit :: save();
    $this->request = $this->toolkit->getRequest();
    $this->response = $this->toolkit->getResponse();
    $this->session = $this->toolkit->getSession();
    $this->session->reset();
    $this->connection = $this->toolkit->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($this->connection);
  }

  function tearDown()
  {
    lmbToolkit :: restore();
  }

  function assertCommandValid($command, $line)
  {
    if($this->assertTrue($command->isValid()))
      return true;

    $errors = array();
    foreach($command->getErrorList() as $error)
      $errors[] .= ' ' .  $error->get();
    $error_text = implode(', ', $errors);
    $this->assertTrue(false, 'Command is not valid with following errors: '. $error_text . ' at line '. $line);

    return false;
  }
}
?>
