<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
* Interface for defining rules to validate against
*/
interface lmbValidationRule
{
  /**
  * Performs validation
  * Validation rules must call {@link lmbErrorList :: addError()} to report about error
  * @see lmbErrorList :: addError()
  * @param lmbSetInterface Datasource to validate
  * @param lmbErrorList List of validation errors
  * @return void
  */
  function validate($datasource, $error_list);
}
?>