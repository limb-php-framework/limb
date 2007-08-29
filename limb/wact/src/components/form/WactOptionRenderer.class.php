<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once 'limb/wact/src/components/form/form.inc.php';

//--------------------------------------------------------------------------------
/**
 * Deals with rendering option elements for HTML select tags
 * Simple renderer for OPTIONs.  Does not support disabled
 * and label attributes. Does not support OPTGROUP tags.
 * @package wact
 * @version $Id$
 */
class WactOptionRenderer
{
  /**
  * Renders an option, sending directly to display.
  * Called from WactSelectSingleComponent or WactSelectMultipleComponent
  * in their renderContents() method
  * @todo XTHML: selected="selected"
  * @param string value to place within the option value attribute
  * @param string contents of the option tag
  * @param boolean whether the option is selected or not
  * @return void
  */
  function renderOption($key, $contents, $selected)
  {
    echo '<option value="';
    echo htmlspecialchars($key, ENT_QUOTES);
    echo '"';
    if ($selected) {
        echo " selected=\"true\"";
    }
    echo '>';
    if (empty($contents)) {
        echo htmlspecialchars($key, ENT_QUOTES);
    } else {
        echo htmlspecialchars($contents, ENT_QUOTES);
    }
    echo '</option>';
  }
}

