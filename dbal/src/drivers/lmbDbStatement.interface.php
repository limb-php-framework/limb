<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * interface lmbDbStatement.
 *
 * @package dbal
 * @version $Id: lmbDbStatement.interface.php 7486 2009-01-26 19:13:20Z pachanga $
 */
interface lmbDbStatement
{
  function setNull($name);
  function setSmallInt($name, $value);
  function setInteger($name, $value);
  function setFloat($name, $value);
  function setDouble($name, $value);
  function setDecimal($name, $value);
  function setBoolean($name, $value);
  function setChar($name, $value);
  function setVarChar($name, $value);
  function setClob($name, $value);
  function setDate($name, $value);
  function setTime($name, $value);
  function setTimeStamp($name, $value);
  function setBlob($name, $value);
  function set($name, $value);
  function import($paramList);
  function getSQL();
  function execute();
  function setConnection($connection);
}


