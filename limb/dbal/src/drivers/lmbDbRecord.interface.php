<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDbRecord.interface.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

lmb_require('limb/core/src/lmbSetInterface.interface.php');

interface lmbDbRecord extends lmbSetInterface
{
  function getInteger($name);
  function getFloat($name);
  function getString($name);
  function getBoolean($name);
  function getIntegerTimeStamp($name);
  function getStringDate($name);
  function getStringTime($name);
  function getStringTimeStamp($name);
  function getStringFixed($name);//???
  function getBlob($name);
}

?>
