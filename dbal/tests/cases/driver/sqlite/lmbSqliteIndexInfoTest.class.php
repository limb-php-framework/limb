<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once(dirname(__FILE__) . '/../DriverIndexInfoTestBase.class.php');
require_once(dirname(__FILE__) . '/fixture.inc.php');

class lmbSqliteIndexInfoTest extends DriverIndexInfoTestBase
{
  protected $_index_names = array(
    'primary' => '(indexes autoindex 1)',
    'unique' => '(indexes autoindex 2)',
    'common' => 'common'
  );

  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    DriverSqliteSetup($this->connection->getConnectionId());
    parent::setUp();
  }
}

