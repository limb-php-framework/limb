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

?>
