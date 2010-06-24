<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/dbal/src/drivers/lmbDbTypeInfo.class.php');
lmb_require('limb/dbal/src/exception/lmbDbException.class.php');

/**
 * class lmbDbColumnInfo.
 *
 * @package dbal
 * @version $Id: lmbDbColumnInfo.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbDbColumnInfo
{
  protected $table;
  protected $name;
  protected $type;
  protected $size;
  protected $scale;
  protected $isNullable;
  protected $defaultValue;

  function __construct($table,
                        $name,
                        $type = null,
                        $size = null,
                        $scale = null,
                        $isNullable = null,
                        $default = null)
  {

    $this->table = $table;
    $this->name = $this->canonicalizeName($name);
    $this->type = $this->canonicalizeType($type);
    $this->size = $this->canonicalizeSize($size);
    $this->scale = $this->canonicalizeScale($scale);
    $this->isNullable = $this->canonicalizeIsNullable($isNullable);
    $this->defaultValue = $this->canonicalizeDefaultValue($default);
  }

  function isValidColumnName($name)
  {
    return preg_match('/[A-Za-z][A-Za-z0-9_]*/', $name);
  }

  function getName()
  {
    return $this->name;
  }

  function canonicalizeName($name)
  {
    if(!$this->isValidColumnName($name))
    {
      throw new lmbDbException("Invalid column name '$name'");
    }
    return $name;
  }

  function getType()
  {
    return $this->type;
  }

  function canonicalizeType($type)
  {
    $typeinfo = new lmbDbTypeInfo();
    $typelist = $typeinfo->getColumnTypeList();
    if(!in_array($type, $typelist))
    {
      throw new lmbDbException("Invalid column type '$type'");
    }
    return $type;
  }

  function getSize()
  {
    return $this->size;
  }

  function canonicalizeSize($size)
  {
    return is_null($size) ?  null : (int) $size;
  }

  function getScale()
  {
    return $this->scale;
  }

  function canonicalizeScale($scale)
  {
    return is_null($scale) ?  null : (int) $scale;
  }

  function getDefaultValue()
  {
    return $this->defaultValue;
  }

  function canonicalizeDefaultValue($defaultValue)
  {
    return $defaultValue;
  }

  function isNullable()
  {
    return $this->isNullable;
  }

  function canonicalizeIsNullable($isNullable)
  {
    return is_null($isNullable) ?  null : (bool) $isNullable;
  }

  function getTable()
  {
    return $this->table;
  }

  function escapeIdentifier($name)
  {
    return $name;
  }
}


