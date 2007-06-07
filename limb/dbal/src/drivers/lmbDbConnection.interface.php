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
 * @version $Id: lmbDbConnection.interface.php 5959 2007-06-07 13:47:57Z pachanga $
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
}

?>