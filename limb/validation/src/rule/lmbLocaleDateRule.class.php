<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    $package$
 */
lmb_require('limb/validation/src/rule/lmbSingleFieldRule.class.php');
lmb_require('limb/i18n/src/datetime/lmbLocaleDate.class.php');

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

    if(!lmbLocaleDate ::  isLocalStringValid($this->locale, $value))
      $this->error(lmb_i18n('{Field} must have a valid date format', 'validation'));
  }
}

?>