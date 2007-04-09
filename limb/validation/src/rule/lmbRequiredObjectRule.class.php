<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbRequiredObjectRule.class.php 5584 2007-04-09 10:43:58Z serega $
 * @package    validation
 */
lmb_require('limb/validation/src/rule/lmbValidationRule.interface.php');

/**
* Checks that field is present in datasource and it's value is an object of some or any class
* Example of usage:
* <code>
*  lmb_require('limb/validation/src/rule/lmbRequiredObjectRule.class.php');
*  $validator->addRule(new lmbRequiredObjectRule('author', 'Author'));
*  // or
*  $validator->addRequiredObjectRule('folder');
* </code>
* @see lmbValidator :: addRequiredObjectRule()
*/
class lmbRequiredObjectRule implements lmbValidationRule
{
  /**
  * @var string Field name
  */
  protected $field_name;
  /**
  * @var string Required class name
  */
  protected $class;
  /**
  * @var string Custom error message
  */
  protected $custom_error;

  /**
  * @param string Field name
  * @param string Required class name
  */
  function __construct($field_name, $class = null, $custom_error = '')
  {
    $this->field_name = $field_name;
    $this->class = $class;
    $this->custom_error = $custom_error;
  }

  /**
  * @see lmbValidationRule :: validate()
  */
  function validate($datasource, $error_list)
  {
    $value = $datasource->get($this->field_name);

    if(!is_object($value) || ($this->class && get_class($value) != $this->class))
    {
      $error = $this->custom_error ? $this->custom_error : lmb_i18n('Object {Field} is required', 'validation');
      $error_list->addError($error, array('Field' => $this->field_name));
      return;
    }
  }
}
?>