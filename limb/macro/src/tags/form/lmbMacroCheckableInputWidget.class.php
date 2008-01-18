<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/tags/form/lmbMacroFormElementWidget.class.php');

/**
 * Represents an HTML input type="radio" and type="checkbox" tags
 * @package macro
 * @version $Id$
 */
class lmbMacroCheckableInputWidget extends lmbMacroFormElementWidget
{
  protected $skip_render = array('checked_value');

  function getName()
  {
    $name = parent :: getName();
    return str_replace('[]', '', $name) ;
  }

  function renderAttributes()
  {
    if($this->isChecked())
      $this->setAttribute('checked', 'true');
    else
      $this->removeAttribute('checked');

    parent :: renderAttributes();
  }

  function getValue()
  {
    if($this->hasAttribute('checked_value'))
      return $this->getAttribute('checked_value');

    return $this->_getValueFromFormDatasource();
  }

  function isChecked()
  {
    $value = $this->getValue();

    // Here we really hard try to guess if it's checked or not...
    if(is_array($value) && in_array($this->getAttribute('value'), $value))
      return true;
    elseif(is_scalar($value) && $value && $value == $this->getAttribute('value'))
      return true;
    elseif($value && !$this->hasAttribute('value'))
      return true;
    elseif($this->hasAttribute('checked') && is_null($value))
      return true;
    elseif($value && $value != $this->getAttribute('value'))
      return false;
    elseif(is_null($value))
      return false;
    elseif(!$value && $this->hasAttribute('value') && !$this->getAttribute('value'))
      return true;

    return false;
  }
}

