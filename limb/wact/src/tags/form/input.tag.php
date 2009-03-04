<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
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
 * @package wact
 * @version $Id: input.tag.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactInputTag extends WactControlTag
{
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/form.inc.php';

  /**
   * Sets the runtimeComponentName property, depending on the type of
   * Input tag
   * @return void
   */
  function prepare()
  {
    $type = strtolower($this->getAttribute('type'));
    switch ($type)
    {
      case 'text':
        $this->runtimeComponentName = 'WactInputComponent';
        break;
      case 'password':
        $this->runtimeComponentName = 'WactFormElementComponent';
        break;
      case 'checkbox':
        $this->runtimeComponentName = 'WactCheckableInputComponent';
        break;
      case 'submit':
        $this->runtimeComponentName = 'WactFormElementComponent';
        break;
      case 'radio':
        $this->runtimeComponentName = 'WactCheckableInputComponent';
        break;
      case 'reset':
        $this->runtimeComponentName = 'WactFormElementComponent';
        break;
      case 'file':
        $this->runtimeComponentName = 'WactFileInputComponent';
        $this->runtimeIncludeFile = 'limb/wact/src/components/form/WactFileInputComponent.class.php';
        break;
      case 'hidden':
        $this->runtimeComponentName = 'WactInputComponent';
        break;
      case 'image':
        $this->runtimeComponentName = 'WactInputComponent';
        break;
      case 'button':
        $this->runtimeComponentName = 'WactInputComponent';
        break;
      default:
        $this->raiseCompilerError('Unrecognized type attribute for input tag');
    }

    parent::prepare();
  }
}

