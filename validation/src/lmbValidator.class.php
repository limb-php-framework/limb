<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/lmbErrorList.class.php');
lmb_require('limb/core/src/lmbHandle.class.php');

/**
 * Holds the list of validation rules along with errors happened during validation.
 * Validates a datasource against added validation rules.
 * @package validation
 * @version $Id: lmbValidator.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbValidator
{
  /**
  * @see lmbValidationRule
  * @var array List of added validation rules
  */
  protected $rules = array();

  /**
  * @var lmbErrorList List of validation errors
  */
  protected $error_list;

  /**
  * Constructor
  * @param lmbErrorList
  */
  function __construct($error_list = null)
  {
    $this->error_list = $error_list;
  }

  /**
  * Returns list of errors.
  * Creates an empty lmbErrorList if error list is NULL
  * @return lmbErrorList
  */
  function getErrorList()
  {
    if(!$this->error_list)
      $this->error_list = new lmbErrorList();

    return $this->error_list;
  }

  /**
  * Sets new list of errors
  * @return void
  */
  function setErrorList($error_list)
  {
    return $this->error_list = $error_list;
  }

  /**
  * Adds a new rule
  * @return void
  */
  function addRule($rule)
  {
    $this->rules[] = $rule;
  }

  /**
  * Alias for adding lmbRequiredRule to validator
  * @return void
  */
  function addRequiredRule($field, $custom_error = '')
  {
    $this->addRule(new lmbHandle('limb/validation/src/rule/lmbRequiredRule',
                                 array($field, $custom_error)));
  }

  function addAtLeastOneRequiredRule($fields, $custom_error = '')
  {
    $this->addRule(new lmbHandle('limb/validation/src/rule/lmbAtleastOneFieldRequiredRule',
                                 array($fields, $custom_error)));
  }

  /**
  * Alias for adding lmbRequiredObjectRule to validator
  * @return void
  */
  function addRequiredObjectRule($field, $class = null, $custom_error = '')
  {
    $this->addRule(new lmbHandle('limb/validation/src/rule/lmbRequiredObjectRule',
                                 array($field, $class, $custom_error)));
  }

  /**
  * Alias for adding lmbSizeRangeRule to validator
  * @return void
  */
  function addSizeRangeRule($field, $min_or_max_length, $max_length = NULL, $custom_error = '')
  {
    $this->addRule(new lmbHandle('limb/validation/src/rule/lmbSizeRangeRule',
                                 array($field, $min_or_max_length, $max_length, $custom_error)));
  }

  /**
  * @return boolean TRUE if list of errors is empty
  */
  function isValid()
  {
    return $this->getErrorList()->isValid();
  }

  /**
  * Performs validation
  * Passes datasource and list of errors to every validation rule
  * @param lmbSetInterface Datasource to validate
  * @return boolean True if valid
  */
  function validate($datasource)
  {
    foreach($this->rules as $rule)
      $rule->validate($datasource, $this->getErrorList());

    return $this->isValid();
  }
}


