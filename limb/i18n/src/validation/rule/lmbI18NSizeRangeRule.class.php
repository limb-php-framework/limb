<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbI18NSizeRangeRule.class.php 5411 2007-03-29 10:07:12Z pachanga $
 * @package    i18n
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');

class lmbI18NSizeRangeRule extends lmbSingleFieldRule
{
  protected $min_length;
  protected $max_length;

  function __construct($field_name, $min_length, $max_length = NULL)
  {
    if (is_null($max_length))
    {
        $this->min_length = NULL;
        $this->max_length = $min_length;
    }
    else
    {
        $this->min_length = $min_length;
        $this->max_length = $max_length;
    }

    parent :: __construct($field_name);
  }

  function check($value)
  {
    if(!is_null($this->min_length) && (lmb_strlen($value) < $this->min_length))
    {
      $this->error(lmb_i18n('{Field} must be greater than {min} characters.', 'validation'),
                   array('min' => $this->min_length));
    }

    if(lmb_strlen($value) > $this->max_length)
    {
      $this->error(lmb_i18n('{Field} must be less than {max} characters.', 'validation'),
                   array('max' => $this->max_length));
    }
  }
}
?>