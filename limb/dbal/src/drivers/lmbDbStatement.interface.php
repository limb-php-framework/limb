<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDbStatement.interface.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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
}

?>
