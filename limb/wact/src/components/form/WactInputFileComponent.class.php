<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactInputFileComponent.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* Represents an HTML input type="file" tag
*
* (Someday someone is actually going to need to upload something.
* Maybe then they will come write some nice methods for this
* tag).
*/
class WactInputFileComponent extends WactInputFormElement
{
  /**
  * We can't get a meaningful 'value' attribute for file upload controls
  * after form submission - the value would need to be the full path to the
  * file on the client machine and we don't have a handle on that
  * information. The component's 'value' is instead set to the relevant
  * portion of the $_FILES array, allowing initial validation of uploaded
  * files w/ WACT.
  */
  function getValue()
  {
  }
}

?>