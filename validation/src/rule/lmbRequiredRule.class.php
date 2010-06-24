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
 * Checks that field is present in datasource and has not empty value
 * Example of usage:
 * <code>
 *  lmb_require('limb/validation/src/rule/lmbRequiredRule.class.php');
 *  $validator->addRule(new lmbRequiredRule('title'));
 *  //or
 *  $validator->addRule(new lmbHandle('limb/validation/src/rule/lmbRequiredRule', array('title')));
 *  // or
 *  $validator->addRequiredRule('title');
 * </code>
 * @see lmbValidator :: addRequiredRule()
 * @package validation
 * @version $Id: lmbRequiredRule.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbRequiredRule extends lmbBaseValidationRule
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
  * Constructor
  * @param string Field name
  */
  function __construct($field_name, $custom_error = '')
  {
    $this->field_name = $field_name;
    $this->custom_error = $custom_error;
  }

  /**
  * @see lmbBaseValidationRule :: _doValidate()
  */
  protected function _doValidate($datasource)
  {
    $value = $datasource->get($this->field_name);
    if(is_null($value) || (is_string($value) && trim($value) === ''))
    {
      $error = $this->custom_error ? $this->custom_error : lmb_i18n('{Field} is required', 'validation');
      $this->error($error, array('Field' => $this->field_name));
    }
  }
}

