<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactExpressionFilterFindingParser.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* Searches expression strings for filters
* Parser expects the initial expression to have been stripped (as happens in Expression class)
**/

class WactExpressionFilterFindingParser
{
  protected $filter_expressions = array();

  protected $current_filter = NULL;

  function __construct($expression)
  {
    $lexer = $this->createExpressionLexer();
    $lexer->parse($expression);

    $this->_addFinalFilterExpression();
  }

  protected function _addFinalFilterExpression()
  {
    if (!is_null($this->current_filter))
      $this->filter_expressions[] = $this->current_filter;
  }

  function getFilterExpressions()
  {
    return $this->filter_expressions;
  }

  function acceptFilterExpressionFromLexer($filter, $state)
  {
    switch ($state)
    {
      case EXPRESSION_LEXER_UNMATCHED:
      case EXPRESSION_LEXER_SPECIAL:
        if (is_null($this->current_filter))
          $this->current_filter = $filter;
        else
          $this->current_filter .= $filter;
      break;
    }
    return TRUE;
  }

  function initNewFilterFromLexer()
  {
    if (!is_null ($this->current_filter))
    {
      $this->filter_expressions[] = $this->current_filter;
      $this->current_filter = NULL;
    }
    return TRUE;
  }

  function createExpressionLexer()
  {
    $lexer = new WactExpressionLexer($this, 'filter');

    $lexer->addSpecialPattern('\|', 'filter', 'add');
    $lexer->addSpecialPattern('"[^"]*"', 'filter', 'doublequote');
    $lexer->addSpecialPattern("'[^']*'", 'filter', 'singlequote');

    $lexer->mapHandler('filter', 'acceptFilterExpressionFromLexer');
    $lexer->mapHandler('doublequote', 'acceptFilterExpressionFromLexer');
    $lexer->mapHandler('singlequote', 'acceptFilterExpressionFromLexer');
    $lexer->mapHandler('add', 'initNewFilterFromLexer');

    return $lexer;
  }
}
?>