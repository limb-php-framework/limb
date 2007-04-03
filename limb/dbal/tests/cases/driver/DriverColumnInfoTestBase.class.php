<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: DriverColumnInfoTestBase.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
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
