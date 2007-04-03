<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: runtimecontent.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
 * Present a named location where content can be inserted at runtime
 * @tag core:RUNTIMECONTENT
 * @forbid_end_tag
 */
class WactCoreRuntimeContentTag extends WactRuntimeComponentTag
{
  /**
   * @param WactCodeWriter
   */
  function postGenerate($code_writer)
  {
    // Perhaps the render() call should be in the generate() method?
    $code_writer->writePHP($this->getComponentRefCode() . '->render();');

    parent::postGenerate($code_writer);
  }
}

?>