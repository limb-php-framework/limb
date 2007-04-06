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

interface lmbDataset extends Iterator, Countable, ArrayAccess
{
  function sort($params);
  function getArray();
  function at($pos);
}

?>