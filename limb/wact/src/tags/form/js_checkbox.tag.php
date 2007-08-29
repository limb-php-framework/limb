<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/tags/form/control.inc.php');
/**
 * @tag js_checkbox
 * @known_parent WactFormTag
 * @suppress_attributes errorclass errorstyle displayname
 * @forbid_end_tag
 * @package wact
 * @version $Id: js_checkbox.tag.php 6243 2007-08-29 11:53:10Z pachanga $
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


