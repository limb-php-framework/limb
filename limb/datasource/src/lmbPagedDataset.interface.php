<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    datasource
 */
lmb_require('limb/datasource/src/lmbDataset.interface.php');

interface lmbPagedDataset extends lmbDataset
{
  function paginate($offset, $limit);
  function getOffset();
  function getLimit();
  function countPaginated();
}

?>