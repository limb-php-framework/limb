<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__) . '/DriverMetaTestBase.class.php');

abstract class DriverColumnInfoTestBase extends DriverMetaTestBase
{
  var $table;

  function setUp()
  {
    $dbinfo = $this->connection->getDatabaseInfo();
    $this->table = $dbinfo->getTable('standard_types');
  }

  function tearDown()
  {
    unset($this->table);
    parent::tearDown();
  }
}



