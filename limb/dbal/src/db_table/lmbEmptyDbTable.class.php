<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbEmptyDbTable.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require('limb/dbal/src/lmbTableGateway.class.php');

class lmbEmptyDbTable extends lmbTableGateway
{
  protected function _defineDbTableName()
  {
    return '';
  }

  protected function _defineColumns()
  {
    return array();
  }
}


?>