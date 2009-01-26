<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');

/**
 * Checks that field value is equal some valid value
 * Example of usage:
 * <code>
 * lmb_require('limb/validation/src/rule/lmbValidValueRule.class.php');
 * $validator->addRule(new lmbValidValueRule("CAPTCHA", 'asdh21'));
 * </code>
 * @package validation
 * @version $Id: lmbInvalidValueRule.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbValidValueRule extends lmbSingleFieldRule
{
  protected $valid_value;

  function __construct($field_name, $valid_value, $custom_error = '')
  {
    parent :: __construct($field_name, $custom_error);

    $this->valid_value = $valid_value;
  }

  function check($value)
  {
    if ($value != $this->valid_value)
    {
      $this->error('{Field} value is wrong');
    }
  }
}


