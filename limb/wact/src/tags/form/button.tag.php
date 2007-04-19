<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: button.tag.php 5688 2007-04-19 11:15:15Z serega $
 * @package    wact
 */

require_once 'limb/wact/src/tags/form/control.inc.php';

/**
 * Compile time component for button tags
 * @tag button
 * @runat client
 * @runat_as WactFormTag
 * @restrict_self_nesting
 * @suppress_attributes errorclass errorstyle displayname
 */
class WactButtonTag extends WactControlTag
{
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/form.inc.php';
  protected $runtimeComponentName = 'WactFormElementTagComponent';
}
?>