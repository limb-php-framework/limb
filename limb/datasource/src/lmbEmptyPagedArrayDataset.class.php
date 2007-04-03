<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbEmptyPagedArrayDataset.class.php 5165 2007-02-28 14:07:13Z tony $
 * @package    datasource
 */
lmb_require('limb/datasource/src/lmbPagedArrayDataset.class.php');

class lmbEmptyPagedArrayDataset extends lmbPagedArrayDataset
{
  function __construct()
  {
    parent :: __construct(array());
  }

  function valid()
  {
    return false;
  }
}
?>