<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/macro/src/tags/form/lmbMacroFormFieldWidget.class.php');

/**
 * class lmbMacroInputWidget
 * A runtime widget for input tag of "text" and "hidden" types
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroInputWidget extends lmbMacroFormFieldWidget
{
  function renderAttributes()
  {
    if(!$this->hasAttribute('value'))
    {
      if($value = $this->getValue())
        $this->setAttribute('value', $value);
      else
        $this->setAttribute('value', "");
    }

   parent :: renderAttributes();
  }
}

