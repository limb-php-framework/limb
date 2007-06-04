<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: preserve_state.tag.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

/**
* @tag form:PRESERVE_STATE
* @forbid_end_tag
* @req_const_attributes name
*/
class WactFormPreserveStateTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $code->writePHP($this->getComponentRefCode() . '->preserveState("' . $this->getAttribute('name') . '");');
  }
}
?>
