<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/dbal/src/query/lmbSelectRawQuery.class.php');

/**
 * class lmbTableRecordsQuery.
 *
 * @package dbal
 * @version $Id: lmbTableRecordsQuery.class.php 6039 2007-07-02 12:51:09Z pachanga $
 */
class lmbTableRecordsQuery extends lmbSelectRawQuery
{
  function __construct($table, $conn)
  {
    $table = $conn->quoteIdentifier($table);
    $sql = "SELECT {$table}.* %fields% FROM {$table} %tables% ".
                                  "%left_join% %where% %group% %having% %order%";

    parent :: __construct($sql, $conn);
  }
}
?>
