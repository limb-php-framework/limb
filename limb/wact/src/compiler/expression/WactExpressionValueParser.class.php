<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactExpressionValueParser.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* Searches expression strings for constant values
* WARNING: this parser defaults to data binding expressions. That means if it
* doest recognise a integer, float or string constant, what it calls a data binding
* expression may not in fact be a data binding expression.
* WactExpressionValueParser assumes that Expression::createvalue asks it a parse a valid value string
*/
class WactExpressionValueParser
{
  const DATABINDING = 0;
  const INT = 1;
  const FLOAT = 2;
  const STRING = 3;

  public $expression_type = self :: DATABINDING;

  public $value;

  public $expression;

  function __construct($expression)
  {
    $lexer = $this->createExpressionLexer();
    $lexer->parse($expression);
  }

  function isConstantvalue()
  {
    return $this->expression_type != WactExpressionValueParser :: DATABINDING;
  }

  function getExpressionType()
  {
    return $this->expression_type;
  }

  function getValue()
  {
    return $this->value;
  }

  function acceptDatabindingFromLexer($expression, $lexer_state)
  {
    if($lexer_state == EXPRESSION_LEXER_UNMATCHED)
    {
      $this->value = self :: DATABINDING;
      $this->value = $expression;
    }
    return TRUE;
  }

  function acceptIntegerFromLexer($int, $lexer_state)
  {
    if($lexer_state == EXPRESSION_LEXER_SPECIAL)
    {
      $this->expression_type = self :: INT;
      $this->value = intval($int);
    }

    return TRUE;
  }

  function acceptFloatFromLexer($float, $lexer_state)
  {
    if($lexer_state == EXPRESSION_LEXER_SPECIAL)
    {
      $this->expression_type = self :: FLOAT;
      $this->value = floatval($float);
    }
    return TRUE;
  }

  function acceptStringFromLexer($string, $lexer_state)
  {
    if($lexer_state == EXPRESSION_LEXER_SPECIAL)
    {
      $this->expression_type = self :: STRING;
      // Strip the quotes (hack but saves introducing further Lexer complexity)
      $string = substr($string, 1, strlen($string) - 2);
      $this->value = $string;
    }
    return TRUE;
  }

  /**
  * Creates the Lexer. Ideally this should be a static instance for
  * performance but Lexer left in strange state after parsing if static
  */
  function createExpressionLexer()
  {
    $lexer = new WactExpressionLexer($this, 'databinding');

    $lexer->addSpecialPattern('^-?\d+$', 'databinding', 'integer');
    $lexer->addSpecialPattern('^-?\d+\.\d+$', 'databinding', 'float');
    $lexer->addSpecialPattern('^".*"$', 'databinding', 'doublequote');
    $lexer->addSpecialPattern('^\'.*\'$', 'databinding', 'singlequote');

    $lexer->mapHandler('databinding', 'acceptDatabindingFromLexer');
    $lexer->mapHandler('integer', 'acceptIntegerFromLexer');
    $lexer->mapHandler('float', 'acceptFloatFromLexer');
    $lexer->mapHandler('doublequote', 'acceptStringFromLexer');
    $lexer->mapHandler('singlequote', 'acceptStringFromLexer');

    return $lexer;
  }
}
?>