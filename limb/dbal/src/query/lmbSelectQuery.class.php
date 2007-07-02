<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/query/lmbSelectRawQuery.class.php');

//TODO: use primitive lexer for parsing sql templates someday...

/**
 * class lmbSelectQuery.
 *
 * @package dbal
 * @version $Id: lmbSelectQuery.class.php 6039 2007-07-02 12:51:09Z pachanga $
 */
class lmbSelectQuery extends lmbSelectRawQuery
{
  function __construct($table, $conn = null)
  {
    parent :: __construct(lmbSelectRawQuery :: DEFAULT_SQL_TEMPLATE, $conn);
    $this->addTable($table);
  }
}
?>
