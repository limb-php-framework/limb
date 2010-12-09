<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/validation/src/rule/lmbValidationRule.interface.php');

/**
 * A base class for validation rules.
 * Implements Composite.
 * @package validation
 * @version $Id$
 */
abstract class lmbBaseValidationRule implements lmbValidationRule
{
  /**
  * @var boolean Flag if validation rule has an error or not
  */
  protected $is_valid = true;

  /**
  * @see validate()
  * @var lmbErrorList List of errors.
  */
  protected $error_list;

  function isValid()
  {
    return $this->is_valid;
  }

  /**
  * Addes an error to error list.
  * Sets "is_valid" flag to false.
  */
  function error($message, $fields = array(), $values = array())
  {
    $this->error_list->addError($message, $fields, $values);
    $this->is_valid = false;
  }

  /**
  * Validates datasource
  * @see lmbValidationRule :: validate
  */
  function validate($datasource, $error_list)
  {
    $this->error_list = $error_list;

    $this->_doValidate($datasource);

    return $this->is_valid;
  }

  abstract protected function _doValidate($datasource);
}

