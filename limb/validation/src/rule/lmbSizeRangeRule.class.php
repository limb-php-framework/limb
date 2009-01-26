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
* For fields have a minimum and maximum length
*/

/**
 * Checks that field has minimux and (or) maximum length
 * Example of usage:
 * <code>
 *  lmb_require('limb/validation/src/rule/lmbSizeRangeRule.class.php');
 *  // restricts "title" field to be more than 50 characters (minimum length is 0)
 *  $validator->addRule(new lmbSizeRangeRule('title', 50));
 *  //same as above
 *  $validator->addRule(new lmbHandle('limb/validation/src/rule/lmbSizeRangeRule', array('title', 10)));
 *  // checks that "title" field have length between 10 and 50 characters
 *  $validator->addRule(new lmbSizeRangeRule('title', 10, 50));
 * </code>
 * @package validation
 * @version $Id: lmbSizeRangeRule.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbSizeRangeRule extends lmbSingleFieldRule
{
  /**
  * @var int Minumum length
  */
  protected $min_length;
  /**
  * @var int Maximum length
  */
  protected $max_length;

  /**
  * Constructor
  * If only two agruments given - use second argument as maximun field length
  * If all three agruments given - use second argument as manimum field length and third - as maximum field length
  * @param string fieldname to validate
  * @param int Minumum or maximum length
  * @param int Maximum length (optional)
  */
  function __construct($field_name, $min_or_max_length, $max_length = NULL, $custom_error = '')
  {
    parent :: __construct($field_name, $custom_error);

    if (is_null($max_length))
    {
      $this->min_length = NULL;
      $this->max_length = $min_or_max_length;
    }
    else
    {
      $this->min_length = $min_or_max_length;
      $this->max_length = $max_length;
    }
  }

  function check($value)
  {
    if (!is_null($this->min_length) && (strlen($value) < $this->min_length))
    {
      $this->error('{Field} must be greater than {min} characters.', array('min' => $this->min_length,
                                                                           'max' => $this->max_length,
                                                                           ));
    }
    elseif (strlen($value) > $this->max_length)
    {
      $this->error('{Field} must be less than {max} characters.', array('max' => $this->max_length,
                                                                        'min' => $this->min_length));
    }
  }
}

