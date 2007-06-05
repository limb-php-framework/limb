<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/rule/lmbValidationRule.interface.php');

class lmbFileUploadPartialRule implements lmbValidationRule
{
  protected $field_name;
  /**
  * @var string Custom error message
  */
  protected $custom_error;

  function __construct($field_name, $custom_error = '')
  {
    $this->field_name = $field_name;
    $this->custom_error = $custom_error;
  }

  function validate($datasource, $error_list)
  {
    $value = $datasource->get($this->field_name);

    if (empty($value['name']))
    {
      $error = $this->custom_error ? $this->custom_error : lmb_i18n('{Field} is required.', 'validation');
      $error_list->addError($error, array('Field' => $this->field_name));
      return false;
    }

    return true;
  }
}
?>