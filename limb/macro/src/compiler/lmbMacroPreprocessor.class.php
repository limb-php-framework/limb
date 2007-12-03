<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbMacroPreprocessor
 *
 * @package macro
 * @version $Id$
 */

class lmbMacroPreprocessor
{
  function process(&$contents)
  {
    $contents = str_replace('<?=', '<?php echo ', $contents);
    $contents = preg_replace('~<\?(?!php|=)~', '<?php ', $contents);    
    $contents = str_replace('$#', '$this->', $contents);
  }
}
