<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: input.tag.php 5688 2007-04-19 11:15:15Z serega $
 * @package    wact
 */

require_once 'limb/wact/src/tags/form/control.inc.php';

/**
 * Compile time component for building runtime InputComponents
 * Creates all the components beginning with the name Input
 * @tag input
 * @forbid_end_tag
 * @runat client
 * @runat_as WactFormTag
 * @restrict_self_nesting
 * @suppress_attributes errorclass errorstyle displayname
 */
class WactInputTag extends WactControlTag
{
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/form.inc.php';

  /**
   * Sets the runtimeComponentName property, depending on the type of
   * Input tag
   * @return void
   */
  function prepare() {
    $type = strtolower($this->getAttribute('type'));
    switch ($type) {
      case 'text':
        $this->runtimeComponentName = 'WactInputTagComponent';
        break;
      case 'password':
        $this->runtimeComponentName = 'WactFormElementTagComponent';
        break;
      case 'checkbox':
        $this->runtimeComponentName = 'WactCheckableInputTagComponent';
        break;
      case 'submit':
        $this->runtimeComponentName = 'WactFormElementTagComponent';
        break;
      case 'radio':
        $this->runtimeComponentName = 'WactCheckableInputTagComponent';
        break;
      case 'reset':
        $this->runtimeComponentName = 'WactFormElementTagComponent';
        break;
      case 'file':
        $this->runtimeComponentName = 'WactFileInputTagComponent';
        $this->runtimeIncludeFile = 'limb/wact/src/components/form/WactFileInputTagComponent.class.php';
        break;
      case 'hidden':
        $this->runtimeComponentName = 'WactInputTagComponent';
        break;
      case 'image':
        $this->runtimeComponentName = 'WactInputTagComponent';
        break;
      case 'button':
        $this->runtimeComponentName = 'WactInputTagComponent';
        break;
      default:
        $this->raiseCompilerError('Unrecognized type attribute for input tag');
    }

    parent::prepare();
  }
}
?>
