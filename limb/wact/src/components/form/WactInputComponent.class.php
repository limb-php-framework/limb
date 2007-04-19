<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    wact
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
?>