<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactTextAreaComponent.
 *
 * @package wact
 * @version $Id$
 */
class WactTextAreaComponent extends WactFormElementComponent
{
  /**
  * Output the contents of the textarea, passing through htmlspecialchars().
  */
  function renderContents()
  {
    echo htmlspecialchars($this->getValue(), ENT_QUOTES);
  }
}


