<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * interface lmbCollectionInterface.
 *
 * @package core
 * @version $Id$
 */
interface lmbCollectionInterface extends Iterator, Countable, ArrayAccess
{
  function sort($params);
  function getArray();
  function at($pos);
  function paginate($offset, $limit);
  function getOffset();
  function getLimit();
  function countPaginated();
}


