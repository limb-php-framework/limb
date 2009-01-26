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
 * Checks that field has minimux and (or) maximum length
 * Example of usage:
 * <code>
 *  lmb_require('limb/validation/src/rule/lmbNumericValueRangeRule.class.php');
 *  // restricts "length" field to have values less than 10.5 and more than 50
 *  $validator->addRule(new lmbNumericValueRangeRule('length', 10.5, 50));
 * </code>
 * @package validation
 * @version $Id: lmbNumericValueRangeRule.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbNumericValueRangeRule extends lmbSingleFieldRule
{
  /**
  * @var float Minimum allowed value
  */
  protected $min_value;
  /**
  * @var float Maximum allowed value
  */
  protected $max_value;

  /**
  * @param string Field name
  * @param float Min value
  * @param float Max value
  */
  function __construct($field_name, $min_value, $max_value, $custom_error = '')
  {
    parent :: __construct($field_name, $custom_error);

    $this->min_value = $min_value;
    $this->max_value = $max_value;
  }

  function check($value)
  {
    if (!preg_match('/^[+-]?(\d*)$/', $value, $match))
      $this->error('{Field} must be a valid number.');

    if($value < $this->min_value)
      $this->error('{Field} must be not less than {value}.', array('value' => $this->min_value));

    if($value > $this->max_value)
    {
      $this->error('{Field} must be not greater than {value}.', array('value' => $this->max_value));
    }
  }
}

