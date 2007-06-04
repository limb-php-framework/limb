<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMysqlRecordSetTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

require_once(dirname(__FILE__) . '/../DriverRecordSetTestBase.class.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbMysqlRecordSetTest extends DriverRecordSetTestBase
{
  function lmbMysqlRecordSetTest()
  {
    parent :: DriverRecordSetTestBase('lmbMysqlRecord');
  }

  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverMysqlSetup($this->connection->getConnectionId());
    parent::setUp();
  }
}

?>
