<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');
lmb_require('limb/i18n/src/datetime/lmbLocaleDateTime.class.php');

/**
 * class lmbLocaleDateRule.
 *
 * @package validation
 * @version $Id$
 */
class lmbLocaleDateRule extends lmbSingleFieldRule
{
  protected $locale;

  function __construct($field_name, $locale = null)
  {
    $this->locale = $locale;
    parent :: __construct($field_name);
  }

  function check($value)
  {
    $toolkit = lmbToolkit :: instance();

    if(!$this->locale)
      $this->locale = $toolkit->getLocaleObject();

    if(!lmbLocaleDateTime ::  isLocalStringValid($this->locale, $value))
      $this->error(lmb_i18n('{Field} must have a valid date format', 'validation'));
  }
}


