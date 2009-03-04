<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */


/**
 * Used to store expressions like "{$var}" found inside tag attributes
 * @package wact
 * @version $Id$
 */
class WactAttributeExpressionFragment implements WactExpressionInterface
{
  protected $expression;

  function __construct($expression, $context, $filter_dictionary)
  {
    $this->expression = new WactExpression($expression, $context, $filter_dictionary, 'raw');
  }

  function isConstant()
  {
    return $this->expression->isConstant();
  }

  function getValue()
  {
    return $this->expression->getValue();
  }

  function generateFragment($code_writer)
  {
    if ($this->isConstant())
    {
      $value = $this->getValue();
      if (!is_null($value))
        $code_writer->writeHTML(htmlspecialchars($value, ENT_QUOTES));
    }
    else
    {
      $code_writer->writePHP('echo htmlspecialchars(');
      $this->expression->generateExpression($code_writer);
      $code_writer->writePHP(', ENT_QUOTES);');
    }
  }

  function generatePreStatement($code_writer)
  {
    $this->expression->generatePreStatement($code_writer);
  }

  function generateExpression($code_writer)
  {
    $this->expression->generateExpression($code_writer);
  }

  function generatePostStatement($code_writer)
  {
    $this->expression->generatePostStatement($code_writer);
  }

  function prepare()
  {
    return $this->expression->prepare();
  }

  function getExpression()
  {
    return $this->expression;
  }
}

