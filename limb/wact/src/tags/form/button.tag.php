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
 * Compile time component for button tags
 * @tag button
 * @runat client
 * @runat_as WactFormTag
 * @restrict_self_nesting
 * @suppress_attributes errorclass errorstyle displayname
 * @package wact
 * @version $Id: button.tag.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactButtonTag extends WactControlTag
{
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/form.inc.php';
  protected $runtimeComponentName = 'WactFormElementComponent';
}

