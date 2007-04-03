<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: textarea.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

require_once 'limb/wact/src/tags/form/control.inc.php';

/**
 * Compile time component for building runtime textarea components
 * @tag textarea
 * @runat_as WactFormTag
 * @suppress_attributes errorclass errorstyle displayname
 * @runat client
 * @restrict_self_nesting
 */
class WactTextAreaTag extends WactControlTag
{
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/form.inc.php';
  protected $runtimeComponentName = 'WactTextAreaComponent';

  function generateContents($code_writer)
  {
    $code_writer->writePHP($this->getComponentRefCode() . '->renderContents();');
  }
}

?>
