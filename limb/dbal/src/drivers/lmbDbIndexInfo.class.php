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
 * @version $Id: lmbDbColumnInfo.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbDbIndexInfo extends lmbObject
{
  protected $table;
  protected $column_name;
  protected $name;
  protected $type;

  const TYPE_COMMON = 1;
  const TYPE_UNIQUE = 2;
  const TYPE_PRIMARY = 3;

  function __construct($table, $column_name, $name, $type)
  {
    $this->table = $table;
    $this->column_name = $column_name;
    $this->name = $name;
    $this->type = $type;
  }

  function getColumn()
  {

  }
}


