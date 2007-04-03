<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbOciColumnInfo.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/drivers/lmbDbColumnInfo.class.php');
lmb_require(dirname(__FILE__) . '/lmbOciTypeInfo.class.php');

class lmbOciColumnInfo extends lmbDbColumnInfo
{
  protected $nativeType;
  protected $isAutoIncrement;
  protected $isExisting = false;

  function __construct(
                $table,
                $name,
                $nativeType = null,
                $size = null,
                $scale = null,
                $isNullable = null,
                $default = null,
                $isAutoIncrement = null,
                $isExisting = false)
  {

    $this->nativeType = $this->canonicalizeNativeType($nativeType);
    $this->isAutoIncrement = $this->canonicalizeIsAutoincrement($isAutoIncrement);

    $typeinfo = new lmbOciTypeInfo();
    $typemap = $typeinfo->getNativeToColumnTypeMapping();
    $type = $typemap[$nativeType];

    $this->isExisting = $isExisting;

    parent::__construct($table, $name, $type, $size, $scale, $isNullable, $default);
  }

  function getNativeType()
  {
    return $this->nativeType;
  }

  function canonicalizeNativeType($nativeType)
  {
    return $nativeType;
  }

  function isAutoIncrement()
  {
    return $this->isAutoIncrement === true;
  }

  function canonicalizeIsAutoIncrement($isAutoIncrement)
  {
    return is_null($isAutoIncrement) ?  null : (bool) $isAutoIncrement;
  }

  function escapeIdentifier($name)
  {
    return "\"$name\"";
  }
}

?>