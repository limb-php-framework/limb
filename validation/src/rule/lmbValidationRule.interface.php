<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Interface for defining rules to validate against
 * @package validation
 * @version $Id: lmbValidationRule.interface.php 7486 2009-01-26 19:13:20Z pachanga $
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

