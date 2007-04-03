<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbEmptyDbTable.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
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