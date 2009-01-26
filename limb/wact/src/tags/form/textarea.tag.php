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
 * Compile time component for building runtime textarea components
 * @tag textarea
 * @runat_as WactFormTag
 * @suppress_attributes errorclass errorstyle displayname
 * @runat client
 * @restrict_self_nesting
 * @package wact
 * @version $Id: textarea.tag.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class WactTextAreaTag extends WactControlTag
{
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/WactTextAreaComponent.class.php';
  protected $runtimeComponentName = 'WactTextAreaComponent';

  function generateTagContent($code_writer)
  {
    $code_writer->writePHP($this->getComponentRefCode() . '->renderContents();');
  }
}


