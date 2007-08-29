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
 * Checks that field value is a valid numeric value and its precision falls
 * within allowable parameters.
 * Example of usage:
 * <code>
 *  lmb_require('limb/validation/src/rule/lmbNumericPrecisionRule.class.php');
 *  $validator->addRule(new lmbNumericPrecisionRule('price', 5, 2));
 *  // 100.2 with match this rule, 100.300 or 100000 - not.
 * </code>
 * @package validation
 * @version $Id: lmbNumericPrecisionRule.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbNumericPrecisionRule extends lmbSingleFieldRule
{
  /**
  * @var int Number of decimal digits allowed
  */
  protected $decimal_digits;
  /**
  * @var int Number of whole digits allowed
  */
  protected $whole_digits;

  /**
  * @param string Field name
  * @param int Number of whole digits allowed
  * @param int Number of decimal digits allowed
  */
  function __construct($field_name, $whole_digits, $decimal_digits = 0, $custom_error = '')
  {
    parent :: __construct($field_name, $custom_error);

    $this->whole_digits = $whole_digits;
    $this->decimal_digits = $decimal_digits;
  }

  function check($value)
  {
    if (preg_match('/^[+-]?(\d*)\.?(\d*)$/', $value, $match))
    {
      if (strlen($match[1]) > $this->whole_digits)
      {
        $this->error('You have entered too many whole digits ({digits}) in {Field} (max {maxdigits}).',
                      array('maxdigits' => $this->whole_digits,
                            'digits' => strlen($match[1])));
      }

      if (strlen($match[2]) > $this->decimal_digits)
      {
        $this->error('You have entered too many decimal digits ({digits}) in {Field} (max {maxdigits}).',
                    array('maxdigits' => $this->decimal_digits,
                          'digits' => strlen($match[2])));
      }
    }
    else
    {
      $this->error('{Field} must be a valid number.');
    }
  }
}

