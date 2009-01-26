<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbMacroTag.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroOutputExpressionNode extends lmbMacroNode
{
  protected $expression;

  function __construct($location, $expression = null)
  {
    $this->expression = $expression;

    parent :: __construct($location);
  }
  
  function setExpression($expression)
  {
    $this->expression = $expression;
  }

  function generate($code)
  {
    $this->expression->preGenerate($code);
    $code->writePHP('echo ' . $this->expression->getValue() .  ";");
  }
}

