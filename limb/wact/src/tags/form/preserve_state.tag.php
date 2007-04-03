<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: preserve_state.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* @tag form:PRESERVE_STATE
* @forbid_end_tag
* @req_const_attributes name
*/
class WactFormPreserveStateTag extends WactCompilerTag
{
  function preGenerate($code)
  {
    $code->writePHP($this->getComponentRefCode() . '->preserveState("' . $this->getAttribute('name') . '");');

    parent :: preGenerate($code);
  }
}
?>
