<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    wact
 */

require_once 'limb/wact/src/compiler/expression/node/expression.inc.php';
require_once 'limb/wact/src/compiler/expression/node/constant.inc.php';
require_once 'limb/wact/src/compiler/expression/node/math.inc.php';
require_once 'limb/wact/src/compiler/expression/node/logical.inc.php';
require_once 'limb/wact/src/compiler/expression/WactDataBindingExpression.class.php';

class WactNewExpressionValueParser
{
  protected $text;
  protected $position;
  protected $length;
  protected $context;

  /**
  * Construct this parser
  */
  function __construct($context)
  {
    $this->context = $context;
  }

  /**
  */
  protected function getToken($pattern) {
    if (preg_match($pattern, $this->text, $match, PREG_OFFSET_CAPTURE, $this->position)) {
      $this->position += strlen($match[0][0]);
      return $match[1][0];
    } else {
      return FALSE;
    }
  }

  /**
  */
  protected function parsePrimary()
  {
    $token = $this->getToken('/\G\s*(#|\^|-|"|\'|\[|\(|[0-9]+|[A-Za-z][A-Za-z0-9_.]*)/u');
    if ($token === FALSE) {
      throw new Exception("expecting primary.");
    }

    if ($token == '-') {
      return new WactUnaryMInusExpressionNode($this->parsePrimary());
    } else if ($token == '(') {
      $expr = $this->parseExpression();
      if ($this->getToken('/\G\s*(\))/u')) {
        return $expr;
      } else {
        throw new Exception('Expecting ).');
      }
    } elseif($token == '^' || $token == '#' || $token == '[') {
      if (!($token2 = $this->getToken('/\G([A-Za-z^][A-Za-z0-9_.\[\]^]*)/u'))) {
        throw new Exception("expecting identifier.");
      }
      return new WactDataBindingExpression($token . $token2, $this->context);
    } else if ($token == '#') {
      if (!($token = $this->getToken('/\G\s*([A-Za-z][A-Za-z0-9_]*)/u'))) {
        throw new Exception("expecting identifier.");
      }
      return new WactRootDataBindingExpressionNode($token);
    } else if ($token == '"' || $token == "'") {
      if ($string = $this->getToken('/\G([^' . $token . ']*)' . $token . '/u')) {
        return new WactConstantExpressionNode($string);
      } else {
        throw new Exception("Expecting a string literal.");
      }
    } else if (ctype_digit($token)) {
      if ($decimalToken = $this->getToken('/\G\.(\d+)/u')) {
        return new WactConstantExpressionNode(floatval($token . '.' . $decimalToken));
      } else {
        return new WactConstantExpressionNode(intval($token));
      }
    } else if (strcasecmp($token, 'and') == 0) {
      throw new Exception('reserved');
    } else if (strcasecmp($token, 'or') == 0) {
      throw new Exception('reserved');
    } else if (strcasecmp($token, 'not') == 0) {
      $expr = $this->parseExpression();
      return new WactLogicalNotExpressionNode($expr);
    } else if (strcasecmp($token, 'null') == 0) {
      return new WactConstantExpressionNode(NULL);
    } else if (strcasecmp($token, 'true') == 0) {
      return new WactConstantExpressionNode(TRUE);
    } else if (strcasecmp($token, 'false') == 0) {
      return new WactConstantExpressionNode(FALSE);
    } else {
      return new WactDataBindingExpression($token, $this->context);
    }
  }

  /**
  * term := primary { '*' primary | '/' primary | '%' primary }
  */
  protected function parseTerm() {

    $term = $this->parsePrimary();

    while ($token = $this->getToken('/\G\s*(\*|\/|%)/u')) {
      $deref = $this->parsePrimary();

      if ($token == '*') {
        $term = new WactMultiplicationExpressionNode($term, $deref);
      } else if ($token == '/') {
        $term = new WactDivisionExpressionNode($term, $deref);
      } else {
        $term = new WactModuloExpressionNode($term, $deref);
      }

    }

    return $term;
  }

  /**
  * sum := term { '+' term | '-' term | '&' term }
  */
  protected function parseSum() {

    $sum = $this->parseTerm();

    while ($token = $this->getToken('/\G\s*(\+|-|&)/u')) {
      $term = $this->parseTerm();

      if ($token == '+') {
        $sum = new WactAdditionExpressionNode($sum, $term);
      } else if ($token == '-') {
        $sum = new WactSubtractionExpressionNode($sum, $term);
      } else {
        $sum = new WactConcatinationExpressionNode($sum, $term);
      }

    }

    return $sum;
  }

  /**
  * comparison :=
  */
  protected function parseComparison() {

    $comparison = $this->parseSum();

    while ($token = $this->getToken('/\G\s*(>=|<=|==|!=|>|<)/u')) {
      $sum = $this->parseSum();

      if ($token == '==') {
        $comparison = new WactEqualToExpressionNode($comparison, $sum);
      } else if ($token == '!=') {
        $comparison = new WactNotEqualToExpressionNode($comparison, $sum);
      } else if ($token == '<') {
        $comparison = new WactLessThanExpressionNode($comparison, $sum);
      } else if ($token == '>') {
        $comparison = new WactGreaterThanExpressionNode($comparison, $sum);
      } else if ($token == '<=') {
        $comparison = new WactLessThanOrEqualToExpressionNode($comparison, $sum);
      } else {
        $comparison = new WactGreaterThanOrEqualToExpressionNode($comparison, $sum);
      }

    }

    return $comparison;
  }

  /**
  * logical := comparison { 'and' comparison | 'or' comparison }
  */
  protected function parseLogical() {

    $logical = $this->parseComparison();

    while ($token = $this->getToken('/\G\s*(or|and)/u')) {
      $comparison = $this->parseComparison();

      if (strcasecmp($token, 'and') == 0) {
        $logical = new WactLogicalAndExpressionNode($logical, $comparison);
      } else {
        $logical = new WactLogicalOrExpressionNode($logical, $comparison);
      }

    }

    return $logical;
  }

  /**
  * expression := logical
  */
  protected function parseExpression() {
    return $this->parseLogical();
  }

  /**
  * Parse text for expressions and emit a stream of events for expression fragments
  */
  function parse($text)
  {
    $this->length = strlen($text);

    if ($this->length == 0) {
      return;
    }

    $this->text = $text;
    $this->position = 0;

    $expression = $this->parseExpression();

    if ($this->position < $this->length &&
      preg_match('/\G\s*$/u', $this->text, $match, PREG_OFFSET_CAPTURE, $this->position)) {
      throw new Exception('Expection end of expression.');
    }

    return $expression;

  }
}
?>