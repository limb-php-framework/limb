<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: js_checkbox.tag.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

require_once('limb/wact/src/tags/form/control.inc.php');
/**
* @tag js_checkbox
* @known_parent WactFormTag
* @suppress_attributes errorclass errorstyle displayname
* @forbid_end_tag
*/
class WactJSCheckboxTag extends WactControlTag
{
  protected $runtimeComponentName = 'WactJSCheckboxComponent';
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/WactJSCheckboxComponent.class.php';

  function prepare()
  {
    $this->setAttribute('type', 'hidden');

    parent :: prepare();
  }

  function getRenderedTag()
  {
    return 'input';
  }

  function generateAfterCloseTag($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->renderJSCheckbox();');
  }
}

?>