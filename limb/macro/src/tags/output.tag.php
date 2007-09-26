<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/macro/src/lmbMacroTag.class.php');

/**
 * class lmbMacroOutputTag.
 *
 * @tag $output
 * @endtag no
 * @package macro
 * @version $Id$
 */
class lmbMacroOutputTag extends lmbMacroTag
{
  function generateContents($code)
  {
    $code->writePHP('echo ' . $this->tag . ';');
  }
}

