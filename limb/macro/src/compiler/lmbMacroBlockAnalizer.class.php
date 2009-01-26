<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbMacroContentAnalizer.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroBlockAnalizer
{
  const BEFORE_CONTENT = 1;
  const EXPRESSION = 2;
  const AFTER_CONTENT = 5;

  protected function _getRegexp()
  {
    return '/^((?s).*?)'. preg_quote('{$', '/') . '((([^\}"\']+["\'][^"\']?["\'])+)?[^}]+)' . preg_quote('}', '/') . '((?s).*)$/';
  }

  function parse($text, $observer)
  {
    // if there is no expression (common case), shortcut this process
    if (strpos($text, '{$') === FALSE)
    {
      $observer->addLiteralFragment($text);
      return;
    }

    $regexp = $this->_getRegexp();

    while (preg_match($regexp, $text, $match))
    {
      if (strlen($match[self :: BEFORE_CONTENT]) > 0)
        $observer->addLiteralFragment($match[self :: BEFORE_CONTENT]);

      $observer->addExpressionFragment('$' . $match[self :: EXPRESSION]);

      $text = $match[self :: AFTER_CONTENT];
    }

    if (strlen($text) > 0)
      $observer->addLiteralFragment($text);
  }
}

