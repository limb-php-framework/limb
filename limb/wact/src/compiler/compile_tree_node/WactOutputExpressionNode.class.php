<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Outputs the result of an expression like {$var} or {$'var'}
 * @package wact
 * @version $Id: WactOutputExpressionNode.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class WactOutputExpressionNode extends WactCompileTreeNode
{
  /**
  * @var WactExpressionInterface
  */
  protected $expression;

  function __construct($location, $expression)
  {
    parent :: __construct($location);

    $this->expression = $expression;
  }

  function prepare()
  {
    $this->expression->prepare();
    parent::prepare();
  }

  function generate($code_writer)
  {
    if ($this->expression->isConstant())
      $code_writer->writeHTML($this->expression->getValue());
    else
    {
      $this->expression->generatePreStatement($code_writer);
      $code_writer->writePHP('echo ');
      $this->expression->generateExpression($code_writer);
      $code_writer->writePHP(';');
      $this->expression->generatePostStatement($code_writer);
    }
  }
}

