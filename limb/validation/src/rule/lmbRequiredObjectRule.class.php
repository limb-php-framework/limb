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
 * Checks that field is present in datasource and it's value is an object of some or any class
 * Example of usage:
 * <code>
 *  lmb_require('limb/validation/src/rule/lmbRequiredObjectRule.class.php');
 *  $validator->addRule(new lmbRequiredObjectRule('author', 'Author'));
 *  // or
 *  $validator->addRequiredObjectRule('folder');
 * </code>
 * @see lmbValidator :: addRequiredObjectRule()
 * @package validation
 * @version $Id: lmbRequiredObjectRule.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbRequiredObjectRule extends lmbBaseValidationRule
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
  * @see lmbBaseValidationRule :: _doValidate()
  */
  protected function _doValidate($datasource)
  {
    $value = $datasource->get($this->field_name);

    if(!is_object($value) || ($this->class && !($value instanceof $this->class)))
    {
      $error = $this->custom_error ? $this->custom_error : lmb_i18n('Object {Field} is required', 'validation');
      $this->error($error, array('Field' => $this->field_name));
      return;
    }
  }
}

