<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDbRecordSet.interface.php 5229 2007-03-13 14:15:40Z serega $
 * @package    dbal
 */
lmb_require('limb/datasource/src/lmbPagedDataset.interface.php');

interface lmbDbRecordSet extends lmbPagedDataset
{
  function freeQuery();
}

?>
