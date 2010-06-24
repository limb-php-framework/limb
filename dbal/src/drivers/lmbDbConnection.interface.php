<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * interface lmbDbConnection.
 *
 * @package dbal
 * @version $Id: lmbDbConnection.interface.php 7961 2009-06-24 06:42:39Z pachanga $
 */
interface lmbDbConnection
{
  function getType();
  function getConnectionId();
  function getHash();
  function getDsnString();
  function connect();
  function disconnect();
  function beginTransaction();
  function commitTransaction();
  function rollbackTransaction();
  function newStatement($sql);
  function execute($sql);
  function executeStatement($stmt);
  function getTypeInfo();
  function getDatabaseInfo();
  function getSequenceValue($table, $colname);
  function quoteIdentifier($id);
  function escape($string);
  function getExtension();
}


