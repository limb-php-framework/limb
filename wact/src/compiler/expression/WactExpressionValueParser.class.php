<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

require_once 'limb/wact/src/compiler/expression/node/WactTemplateExpressionNode.class.php';
require_once 'limb/wact/src/compiler/expression/node/WactConstantExpressionNode.class.php';
require_once 'limb/wact/src/compiler/expression/node/WactBinaryExpressionNode.class.php';
require_once 'limb/wact/src/compiler/expression/node/WactParenthesisExpressionNode.class.php';
require_once 'limb/wact/src/compiler/expression/node/WactUnaryExpressionNode.class.php';
require_once 'limb/wact/src/compiler/expression/node/WactDataBindingExpressionNode.class.php';

/**
 * class WactExpressionValueParser.
 *
 * @package wact
 * @version $Id: WactExpressionValueParser.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactExpressionValueParser
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

  function raiseError($message, $params = array())
  {
    $this->context->raiseCompilerError($message, $params);
  }

  /**
  */
  protected function getToken($pattern)
  {
    if (preg_match($pattern, $this->text, $match, PREG_OFFSET_CAPTURE, $this->position))
    {
      $this->position += strlen($match[0][0]);
      return $match[1][0];
    }
    else
      return FALSE;
  }

  /**
  */
  protected function parsePrimary()
  {
    $token = $this->getToken('/\G\s*(#|\^|\$|:|-|\.[0-9]+|"|\'|!|\[|\(|[0-9]+|[A-Za-z][A-Za-z0-9_.]*)/u');
    if ($token === FALSE)
      $this->raiseError("Expecting primary operand in expression.");

    if ($token == '-')
      return new WactUnaryExpressionNode($this->parsePrimary(), '-');

    if ($token == '(')
    {
      $expr = $this->parseExpression();
      if ($this->getToken('/\G\s*(\))/u'))
        return new WactParenthesisExpressionNode($expr);
      else
        $this->raiseError('Expecting ) in expression');
    }
    // one of the DBE context modifier
    elseif($token == '^' || $token == '#' || $token == '[' || $token == ':')
    {
      if (!($token2 = $this->getToken('/\G([A-Za-z^:\[][A-Za-z0-9_.\[\]^]*)/u')))
        $this->raiseError("Expecting identifier after DBE modifier.");

      return new WactDataBindingExpressionNode($token . $token2, $this->context);
    }
    // php variable
    elseif($token == '$')
    {
      if (!($token2 = $this->getToken('/\G([A-Za-z][A-Za-z0-9_.\[\]^]*)/u')))
        $this->raiseError("Expecting variable name after \$ symbol.");

      return new WactDataBindingExpressionNode($token . $token2, $this->context);
    }
    // string
    elseif ($token == '"' || $token == "'")
    {
      $string = $this->getToken('/\G([^' . $token . ']*)' . $token . '/u');
      if ($string !== FALSE)
        return new WactConstantExpressionNode($string);
      else
        $this->raiseError("Expecting a string literal.");
    }
    // integer or float
    elseif (ctype_digit($token))
    {
      if ($decimalToken = $this->getToken('/\G\.(\d+)/u'))
        return new WactConstantExpressionNode(floatval($token . '.' . $decimalToken));
      else
        return new WactConstantExpressionNode(intval($token));
    }
    // logical not
    elseif($token == '!')
    {
      $expr = $this->parseExpression();
      return new WactUnaryExpressionNode($expr, '!');
    }
    elseif (strcasecmp($token, 'null') == 0)
      return new WactConstantExpressionNode(NULL);
    elseif (strcasecmp($token, 'true') == 0)
      return new WactConstantExpressionNode(TRUE);
    elseif (strcasecmp($token, 'false') == 0)
      return new WactConstantExpressionNode(FALSE);
    elseif(strcasecmp($token, '.') == 0)
      return new WactDataBindingExpressionNode(substr($token, 1), $this->context);
    else
      return new WactDataBindingExpressionNode($token, $this->context);
  }

  protected function parseOperators()
  {
    $sum = $this->parsePrimary();

    while ($token = $this->getToken('/\G\s*(\*|\/|%|\+|-|>=|<=|==|!=|>|<|\|\||&&|&|\!)/u'))
    {
      $term = $this->parseOperators();
      $sum = new WactBinaryExpressionNode($sum, $term, $token);
    }

    return $sum;
  }

  protected function parseExpression()
  {
    return $this->parseOperators();
  }

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
      $this->raiseError('Expection end of expression.');
    }

    return $expression;

  }
}

