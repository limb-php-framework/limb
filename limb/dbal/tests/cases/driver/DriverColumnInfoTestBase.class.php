<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: DriverColumnInfoTestBase.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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


?>
