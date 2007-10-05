<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/lmbMacroNode.class.php');

/**
 * class lmbMacroTag.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroOutputExpressionNode extends lmbMacroNode
{
  protected $expression;

  function __construct($location, $expression)
  {
    $this->expression = $expression;

    parent :: __construct($location);
  }

  function generateContents($code)
  {
    $this->expression->preGenerate($code);
    $code->writePHP('echo ' . $this->expression->getValue() .  ";\n");
  }
}

