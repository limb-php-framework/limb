<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbValidationRule.interface.php 5106 2007-02-18 09:23:45Z serega $
 * @package    validation
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
  * @param lmbDatasource Datasource to validate
  * @param lmbErrorList List of validation errors
  * @return void
  */
  function validate($datasource, $error_list);
}
?>