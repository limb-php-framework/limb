<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__) . '/DriverMetaTestBase.class.php');

abstract class DriverIndexInfoTestBase extends DriverMetaTestBase
{
  /**
   * @var lmbDbTableInfo
   */
  var $table;

  function setUp()
  {
    $dbinfo = $this->connection->getDatabaseInfo();
    $this->table = $dbinfo->getTable('indexes');
  }

  function tearDown()
  {
    unset($this->table);
    parent::tearDown();
  }
}



