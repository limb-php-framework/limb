<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbOciTypeInfoTest.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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
