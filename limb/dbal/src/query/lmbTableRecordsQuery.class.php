<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbTableRecordsQuery.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/query/lmbSelectQuery.class.php');

class lmbTableRecordsQuery extends lmbSelectQuery
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
