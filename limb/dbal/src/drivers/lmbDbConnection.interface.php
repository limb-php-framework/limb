<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * interface lmbDbConnection.
 *
 * @package dbal
 * @version $Id: lmbDbConnection.interface.php 6176 2007-07-19 08:25:31Z pachanga $
 */
interface lmbDbConnection
{
  function getType();
  function getConnectionId();
  function getHash();
  function connect();
  function disconnect();
  function beginTransaction();
  function commitTransaction();
  function rollbackTransaction();
  function newStatement($sql);
  function execute($sql);
  function getTypeInfo();
  function getDatabaseInfo();
  function getSequenceValue($table, $colname);
  function quoteIdentifier($id);
  function escape($string);
}

?>
