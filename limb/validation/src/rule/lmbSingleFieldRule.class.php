<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/validation/src/rule/lmbBaseValidationRule.class.php');

/**
 * A base class for rules responsbile for validating a single field should inherit this class.
 * @package validation
 * @version $Id: lmbSingleFieldRule.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
abstract class lmbSingleFieldRule extends lmbBaseValidationRule
{
  /**
  * @var string Field name
  */
  protected $field_name;

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
    parent :: error($error, array('Field' => $this->field_name), $values);
  }

  /**
  * Validates field
  * Calls {@link check()} method if $datasource has such field with not empty value.
  * Child classes must implement check($value) method to perform real validation.
  * To check field for existance and having none empty value use {@link lmbRequiredRule}
  * Fills {@link $error_list}
  * @see lmbBaseValidationRule :: _doValidate()
  */
  protected function _doValidate($datasource)
  {
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

