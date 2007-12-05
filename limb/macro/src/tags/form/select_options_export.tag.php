<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/tags/form/lmbMacroFormElementTag.class.php');

/**
 * @tag select_options_export
 * @req_attributes from, to, text_field, key_field
 * @forbid_end_tag
 * @package macro
 * @version $Id$
 */
class lmbMacroSelectOptionsExportTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $to = $this->get('to');
    $from = $this->get('from');
    
    $key_field = $this->get('key_field');
    $text_field = $this->get('text_field');
    
    $options = $code->generateVar();
    $code->writePHP("{$options} = array();\n");
    
    $code->writePHP("foreach({$from} as \$item) {\n");
    
    $code->writePHP("if(isset(\$item['{$key_field}']) && isset(\$item['{$text_field}']))\n");
    $code->writePHP("{$options}[\$item['{$key_field}']] = \$item['{$text_field}'];\n");
      
    $code->writePHP("}\n");
 
    $code->writePHP("{$to} = {$options};\n");
  }   
}
