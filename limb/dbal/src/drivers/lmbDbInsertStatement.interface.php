<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDbInsertStatement.interface.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

lmb_require('limb/dbal/src/drivers/lmbDbManipulationStatement.interface.php');

interface lmbDbInsertStatement extends lmbDbManipulationStatement
{
  function insertId($field_name = 'id');
}

?>
