<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactBlockAnalizer.
 *
 * @package wact
 * @version $Id$
 */
class WactBlockAnalizer
{
  const BEFORE_CONTENT = 1;
  const EXPRESSION = 2;
  const AFTER_CONTENT = 5;

  protected $variable_reference_pattern;

  function __construct()
  {
    $this->variable_reference_pattern = $this->_getVariableReferentPattern();
  }

  protected function _getVariableReferentPattern()
  {
    $pattern =
      // start at the beginning
      '/^' .
      // Pick up the portion of the string before the variable reference
      '((?s).*?)' .
      // Beginning of a variable reference
      preg_quote('{$', '/') .
      // Collect the entire variable reference into one subexpression
      '(' .
          // capture the contents of one or more fragments.
          '(' .
              // Anything thats not a quote or the end of the variable
              // reference can be in a fragment
              '[^"\'}]+' .
              // OR
              '|' .
              // A string inside quotes is also a fragment
              '(\'|").*?\4' .
          ')+' .
      ')' .
      // end of a variable reference
      preg_quote('}', '/') .
      // Pick up the portion of the string after the variable reference
      // This portion may contain additional references; we only match
      // one at a time.
      '((?s).*)' .
      // Match until the end of the string
      '$/';

    return $pattern;
  }

  function parse($text, $observer)
  {
    // if there is no expression (common case), shortcut this process
    if (strpos($text, '{$') === FALSE)
    {
      $observer->addLiteralFragment($text);
      return;
    }

    while (preg_match($this->variable_reference_pattern, $text, $match))
    {
      if (strlen($match[self :: BEFORE_CONTENT]) > 0)
        $observer->addLiteralFragment($match[self :: BEFORE_CONTENT]);

      $observer->addExpressionFragment($match[self :: EXPRESSION]);

      $text = $match[self :: AFTER_CONTENT];
    }

    if (strlen($text) > 0)
      $observer->addLiteralFragment($text);
  }
}

