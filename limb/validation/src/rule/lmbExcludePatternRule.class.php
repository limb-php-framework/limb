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
 * Checks that field value doesn't match some regexp.
 * In other words this rule triggers validation error if field value matches regexp.
 * Example of usage:
 * <code>
 * lmb_require('limb/validation/src/rule/lmbExcludePatternRule.class.php');
 * $validator->addRule(new lmbExcludePatternRule("title", "/[^a-zA-Z0-9.-]+/i"));
 * </code>
 * @package validation
 * @version $Id: lmbExcludePatternRule.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbExcludePatternRule extends lmbSingleFieldRule
{
  /**
  * @var string Pattern to match against
  */
  protected $pattern;

  /**
  * @param string Field name
  * @param string Pattern to match against
  */
  function __construct($field_name, $pattern, $custom_error = '')
  {
    parent :: __construct($field_name, $custom_error);

    $this->pattern = $pattern;
  }

  function check($value)
  {
    if (preg_match($this->pattern, $value))
    {
      $this->error('{Field} value is wrong');
    }
  }
}

