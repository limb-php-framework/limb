<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDbRecord.interface.php 5645 2007-04-12 07:13:10Z pachanga $
 * @package    dbal
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
