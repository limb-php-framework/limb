<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__) . '/lmbARTestingObjectMother.class.php'); 

class lmbARBaseTestCase extends UnitTestCase
{
  protected $conn;
  protected $db;
  protected $creator;
  protected $tables_to_cleanup = array();

  function setUp()
  {
    $toolkit = lmbToolkit :: save();
    $this->conn = $toolkit->getDefaultDbConnection();
    $this->db = new lmbSimpleDb($this->conn);
    $this->creator = new lmbARTestingObjectMother($this->conn);

    $this->_cleanUp();
  }

  function tearDown()
  {
    $this->_cleanUp();

    $this->conn->disconnect();

    lmbToolkit :: restore();
  }

  protected function _cleanUp()
  {
    foreach($this->tables_to_cleanup as $table_name)
      $this->db->delete($table_name);
  }
}
