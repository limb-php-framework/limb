<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: DriverDatabaseInfoTestBase.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
require_once(dirname(__FILE__) . '/DriverMetaTestBase.class.php');

abstract class DriverDatabaseInfoTestBase extends DriverMetaTestBase
{
  var $dbinfo;

  function setUp()
  {
    $this->dbinfo = $this->connection->getDatabaseInfo();
  }

  function testHasTable()
  {
    $this->assertTrue($this->dbinfo->hasTable('founding_fathers'));
    $this->assertTrue($this->dbinfo->hasTable('standard_types'));
  }

  function testGetTable()
  {
    $table = $this->dbinfo->getTable('founding_fathers');
    $this->assertIsA($table, 'lmbDbTableInfo');
  }
}

?>
