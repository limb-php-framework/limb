<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbOciStatementTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
require_once(dirname(__FILE__) . '/../DriverStatementTestBase.class.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbOciStatementTest extends DriverStatementTestBase
{
  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverOciSetup($this->connection->getConnectionId());
    parent::setUp();
  }

  //these two tested separately
  function testSetText(){}
  function testSetBlob(){}

  function testSetDate()
  {
    echo "Skipping ".__FUNCTION__." (not yet implemented)\n";
  }

  function testSetTime()
  {
    echo "Skipping ".__FUNCTION__." (not yet implemented)\n";
  }

  function testSetTimeStamp()
  {
    echo "Skipping ".__FUNCTION__." (not yet implemented)\n";
  }

}

?>
