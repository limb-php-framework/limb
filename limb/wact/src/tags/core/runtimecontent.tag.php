<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Present a named location where content can be inserted at runtime
 * @tag core:RUNTIMECONTENT
 * @forbid_end_tag
 * @package wact
 * @version $Id: runtimecontent.tag.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class WactCoreRuntimeContentTag extends WactRuntimeComponentTag
{
  /**
   * @param WactCodeWriter
   */
  function generate($code_writer)
  {
    // Perhaps the render() call should be in the generate() method?
    $code_writer->writePHP($this->getComponentRefCode() . '->render();');
  }
}


