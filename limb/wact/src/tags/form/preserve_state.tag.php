<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @tag form:PRESERVE_STATE
 * @forbid_end_tag
 * @req_const_attributes name
 * @package wact
 * @version $Id: preserve_state.tag.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class WactFormPreserveStateTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $code->writePHP($this->getComponentRefCode() . '->preserveState("' . $this->getAttribute('name') . '");');
  }
}

