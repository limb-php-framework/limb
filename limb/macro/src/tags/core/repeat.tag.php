<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Repeat a portion of the template several times
 * @tag repeat
 * @req_attributes times  
 * @package macro
 * @version $Id$
 */
class lmbMacroRepeatTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $counter = $code->generateVar();     
    $times = $this->get('times');
      
    $code->writePhp("for ($counter = 0; $counter < $times; $counter++ ){ \n");

    parent :: _generateContent($code);

    $code->writePhp("}\n");
  }
}

