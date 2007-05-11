<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSingleFieldRule.class.php 5867 2007-05-11 14:13:55Z pachanga $
 * @package    validation
 */
lmb_require('limb/validation/src/rule/lmbValidationRule.interface.php');
lmb_require('limb/i18n/common.inc.php');

/**
* A base class for rules responsbile for validating a single field should inherit this class.
*/
abstract class lmbSingleFieldRule implements lmbValidationRule
{
  /**
  * @var string Field name
  */
  protected $field_name;
  /**
  * @see validate()
  * @var lmbErrorList List of errors.
  */
  protected $error_list;
  /**
  * @var string Custom error message
  */
  protected $custom_error;

  /**
  * @param string Field name
  */
  function __construct($field_name, $custom_error = '')
  {
    $this->field_name = $field_name;
    $this->custom_error = $custom_error;
  }

  /**
  * @return string Field name
  */
  function getField()
  {
    return $this->field_name;
  }

  /**
  * Alias for adding single field error to error list
  * Fills field array with array('Field' => $this->field_name) that is ok for single field rules
  * If $custom_error attribute is set will use $custom_error regardless of $message
  * If $custom_error attribute is not set will apply lmb_i18n function to $message
  * @param string Error message
  * @param array Array of values
  * @see lmbErrorList :: addError()
  * @return void
  */
  function error($message, $values = array(), $i18n_params = array())
  {
    $error = $this->custom_error ? $this->custom_error : lmb_i18n($message, $i18n_params, 'validation');
    $this->error_list->addError($error, array('Field' => $this->field_name), $values);
  }

  /**
  * Validates field
  * Calls {@link check()} method if $datasource has such field with not empty value.
  * Child classes must implement check($value) method to perform real validation.
  * To check field for existance and having none empty value use {@link lmbRequiredRule}
  * Fills {@link $error_list}
  * @see lmbValidationRule :: validate
  */
  function validate($datasource, $error_list)
  {
    $this->error_list = $error_list;
    $value = $datasource->get($this->field_name);
    if(isset($value) && $value !== '')
      $this->check($value);
  }

  /**
  * Performs real validation
  * @param mixed Field value check
  * @return void
  */
  abstract function check($value);
}
?>