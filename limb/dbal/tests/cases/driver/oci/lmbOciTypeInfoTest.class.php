<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbOciTypeInfoTest.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
require_once(dirname(__FILE__) . '/../DriverTypeInfoTestBase.class.php');

class lmbOciTypeInfoTest extends DriverTypeInfoTestBase
{
  function lmbOciTypeInfoTest()
  {
    parent :: DriverTypeInfoTestBase('lmbOciStatement', 'lmbOciRecord');
  }

  function setUp()
  {
    $this->connection = lmbToolkit :: instance()->getDefaultDbConnection();
    $this->typeInfo = $this->connection->getTypeInfo();
    parent::setUp();
  }
}

?>
