<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSingleFieldRule.class.php 5400 2007-03-29 07:10:15Z pachanga $
 * @package    validation
 */
lmb_require('limb/validation/src/rule/lmbValidationRule.interface.php');

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
  * @param string Field name
  */
  function __construct($field_name)
  {
    $this->field_name = $field_name;
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
  *
  * @param string Error message
  * @param array Array of values
  * @see lmbErrorList :: addError()
  * @return void
  */
  function error($message, $values = array())
  {
    $this->error_list->addError($message, array('Field' => $this->field_name), $values);
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
    if (isset($value) && $value !== '')
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