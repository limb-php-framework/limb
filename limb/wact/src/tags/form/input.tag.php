<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: input.tag.php 5686 2007-04-19 10:35:03Z serega $
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
        $this->runtimeComponentName = 'WactInputFormElement';
        break;
      case 'password':
        $this->runtimeComponentName = 'WactFormElement';
        break;
      case 'checkbox':
        $this->runtimeComponentName = 'WactCheckableFormElement';
        break;
      case 'submit':
        $this->runtimeComponentName = 'WactFormElement';
        break;
      case 'radio':
        $this->runtimeComponentName = 'WactCheckableFormElement';
        break;
      case 'reset':
        $this->runtimeComponentName = 'WactFormElement';
        break;
      case 'file':
        $this->runtimeComponentName = 'WactInputFileComponent';
        $this->runtimeIncludeFile = 'limb/wact/src/components/form/WactInputFileComponent.class.php';
        break;
      case 'hidden':
        $this->runtimeComponentName = 'WactInputFormElement';
        break;
      case 'image':
        $this->runtimeComponentName = 'WactInputFormElement';
        break;
      case 'button':
        $this->runtimeComponentName = 'WactInputFormElement';
        break;
      default:
        $this->raiseCompilerError('Unrecognized type attribute for input tag');
    }

    parent::prepare();
  }
}
?>
