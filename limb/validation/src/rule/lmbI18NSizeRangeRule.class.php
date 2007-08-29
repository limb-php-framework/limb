<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');
lmb_require('limb/i18n/utf8.inc.php');

/**
 * class lmbI18NSizeRangeRule.
 *
 * @package validation
 * @version $Id$
 */
class lmbI18NSizeRangeRule extends lmbSingleFieldRule
{
  /**
   * @var integer	min field value length in glyphs
   */
  protected $min_length;
  /**
   * @var integer max field value length in glyphs
   */
  protected $max_length;

  function __construct($field_name, $min_length, $max_length = NULL, $custom_error = '')
  {
    if(is_null($max_length))
    {
      $this->min_length = null;
      $this->max_length = $min_length;
    }
    else
    {
      $this->min_length = $min_length;
      $this->max_length = $max_length;
    }

    parent :: __construct($field_name, $custom_error);
  }

  function check($value)
  {
    if(!is_null($this->min_length) && (lmb_strlen($value) < $this->min_length))
    {
    	$this->error(lmb_i18n('{Field} must be greater than {min} and less than {max} characters.', 'validation'),
                   array('min' => $this->min_length, 'max' => $this->max_length));
    }

    if(lmb_strlen($value) > $this->max_length)
    {
      $this->error(lmb_i18n('{Field} must be less than {max} and greater than {min} characters.', 'validation'),
                   array('min' => $this->min_length, 'max' => $this->max_length));
    }
  }
}

