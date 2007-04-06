<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDbRecord.interface.php 5559 2007-04-06 13:05:29Z pachanga $
 * @package    dbal
 */

lmb_require('limb/datasource/src/lmbDatasource.interface.php');

interface lmbDbRecord extends lmbDatasource
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
