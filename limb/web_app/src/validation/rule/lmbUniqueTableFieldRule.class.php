<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbUniqueTableFieldRule.class.php 5414 2007-03-29 10:08:19Z pachanga $
 * @package    web_app
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');

class lmbUniqueTableFieldRule extends lmbSingleFieldRule
{
  protected $table_name = '';
  protected $table_field = '';
  protected $error_message = '';

  function __construct($field_name, $table_name, $table_field = '', $error_message = '')
  {
    parent :: __construct($field_name);

    $this->table_name = $table_name;
    $this->table_field = $table_field ? $table_field : $field_name;
    $this->error_message = $error_message;
  }

  function check($value)
  {
    $conn = lmbToolkit :: instance()->getDefaultDbConnection();

    $sql = 'SELECT *
            FROM ' . $this->table_name . '
            WHERE  ' . $this->table_field . '=:value:';

    $stmt = $conn->newStatement($sql);
    $stmt->setVarChar('value', $value);
    $rs = $stmt->getRecordSet();

    if($rs->count() == 0)
      return;

    if($this->error_message)
      $this->error($this->error_message, array('Value' => $value));
    else
      $this->error(lmb_i18n('{Field} must have other value since {Value} already exists', 'web_app'),
                   array('Value' => $value));
  }
}
?>