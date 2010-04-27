<?php
 /*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2010 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @tag mailpart
 * @aliases 
 * @req_attributes name
 * @restrict_self_nesting
 */
class lmbMailpartTag extends lmbMacroTag
{ 
  function preParse($compiller)
  {
     if(!$this->has('name')) throw new lmbMacroException('Tag {{mailpart}}, required attribute "name"');
     parent :: preParse($compiller);
  }
  
  function _generateBeforeContent($code_writer)
  {
    $code_writer->writeHTML('<mailpart name="'.$this->get('name').'"><![CDATA[');
  }

  function _generateAfterContent($code_writer)
  {
  	$code_writer->writeHTML(']]></mailpart>');
  }
}