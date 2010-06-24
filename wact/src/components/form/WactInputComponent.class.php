<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactInputComponent.
 *
 * @package wact
 * @version $Id$
 */
class WactInputComponent extends WactFormElementComponent
{
  function renderAttributes()
  {
    $value = $this->getValue();
    if (!is_null($value))
      $this->setAttribute('value', $value);
    else
      $this->setAttribute('value', '');

   parent :: renderAttributes();
  }
}

