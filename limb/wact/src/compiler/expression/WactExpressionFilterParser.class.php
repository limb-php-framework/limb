<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactExpressionFilterParser.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* Parses a single filter expression
* WARNING: this parser expects strings parsed by WactExpressionFilterFindingParser
*/
class WactExpressionFilterParser
{
  protected $filter_name = NULL;
  protected $filter_arguments = NULL;
  protected $current_argument = NULL;

  function __construct($expression)
  {
    $lexer = $this->createExpressionLexer();
    $lexer->parse($expression);

    // Make sure remaining argument added
    if (!is_null ($this->filter_arguments) && !is_null($this->current_argument))
      $this->filter_arguments[] = $this->current_argument;
  }

  function getFilterName()
  {
    return $this->filter_name;
  }

  function getFilterArguments()
  {
    return $this->filter_arguments;
  }

  function initArgumentsFromLexer()
  {
    $this->filter_arguments = array();
    return TRUE;
  }

  function acceptDelimeterFromLexer()
  {
    if (!is_null($this->filter_arguments) && !is_null($this->current_argument))
    {
      $this->filter_arguments[] = $this->current_argument;
      $this->current_argument = NULL;
    }
    return TRUE;
  }

  function acceptArgumentOrFilterfilter_nameFromLexer($expression, $state)
  {
    switch ($state)
    {
      case EXPRESSION_LEXER_UNMATCHED:
        if(is_null($this->filter_arguments) && is_null($this->filter_name))
          $this->filter_name = $expression;
        else
          $this->_addToCurrentArgument($expression);
      break;

      case EXPRESSION_LEXER_SPECIAL:
        if ( !is_null($this->filter_arguments) )
          $this->_addToCurrentArgument($expression);
      break;
    }

    return TRUE;
  }

  protected function _addToCurrentArgument($expression)
  {
    if ( is_null($this->current_argument) )
      $this->current_argument = $expression;
    else
      $this->current_argument .= $expression;
  }

  function createExpressionLexer()
  {
    $lexer = new WactExpressionLexer($this, 'value');

    $lexer->addSpecialPattern(':', 'value', 'args');
    $lexer->addSpecialPattern(',', 'value', 'arg');

    $lexer->addSpecialPattern('"[^"]*"', 'value', 'doublequote');
    $lexer->addSpecialPattern("'[^']*'", 'value', 'singlequote');

    $lexer->addPattern('\s', 'value');

    $lexer->mapHandler('value', 'acceptArgumentOrFilterfilter_nameFromLexer');
    $lexer->mapHandler('args', 'initArgumentsFromLexer');
    $lexer->mapHandler('arg', 'acceptDelimeterFromLexer');
    $lexer->mapHandler('doublequote', 'acceptArgumentOrFilterfilter_nameFromLexer');
    $lexer->mapHandler('singlequote', 'acceptArgumentOrFilterfilter_nameFromLexer');

    return $lexer;
  }
}
?>