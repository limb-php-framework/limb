<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
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


