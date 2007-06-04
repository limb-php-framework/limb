<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDbConnection.interface.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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