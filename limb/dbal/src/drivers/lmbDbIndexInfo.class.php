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
 * class lmbDbIndexInfo.
 *
 * @property lmbDbTableInfo $table
 * @property string $name
 * @property string $column_name
 * @property integer $type
 *
 * @package dbal
 * @version $Id: lmbDbColumnInfo.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbDbIndexInfo extends lmbObject
{
  const TYPE_COMMON = 1;
  const TYPE_UNIQUE = 2;
  const TYPE_PRIMARY = 3;


  function isCommon()
  {
    return self::TYPE_COMMON === $this->type;
  }

  function isUnique()
  {
    return self::TYPE_UNIQUE === $this->type;
  }

  function isPrimary()
  {
    return self::TYPE_PRIMARY === $this->type;
  }
}


