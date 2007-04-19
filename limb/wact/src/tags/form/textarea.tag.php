<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: textarea.tag.php 5688 2007-04-19 11:15:15Z serega $
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
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/WactTextAreaTagComponent.class.php';
  protected $runtimeComponentName = 'WactTextAreaTagComponent';

  function generateContents($code_writer)
  {
    $code_writer->writePHP($this->getComponentRefCode() . '->renderContents();');
  }
}

?>
