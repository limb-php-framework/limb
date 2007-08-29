<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');

/**
 * Checks that field value is not equal some invalid value
 * Example of usage:
 * <code>
 * lmb_require('limb/validation/src/rule/lmbInvalidValueRule.class.php');
 * $validator->addRule(new lmbInvalidValueRule("region", -1));
 * </code>
 * @package validation
 * @version $Id: lmbInvalidValueRule.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbInvalidValueRule extends lmbSingleFieldRule
{
  protected $invalid_value;

  function __construct($field_name, $invalid_value, $custom_error = '')
  {
    parent :: __construct($field_name, $custom_error);

    $this->invalid_value = $invalid_value;
  }

  function check($value)
  {
    $invalid_value = $this->invalid_value;

    settype($invalid_value, 'string');//???

    if ($value == $invalid_value)
    {
      $this->error('{Field} value is wrong');
    }
  }
}


