<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDbRecordSet.interface.php 5645 2007-04-12 07:13:10Z pachanga $
 * @package    dbal
 */
lmb_require('limb/core/src/lmbCollectionInterface.interface.php');

interface lmbDbRecordSet extends lmbCollectionInterface
{
  function freeQuery();
}

?>
