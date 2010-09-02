<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbMacroClipFilter
 * Clipping the string by given lenght. Multibyte unsafe
 *
 * @filter clip
 * @package macro
 * @version $Id$
 */
class lmbMacroClipFilter extends lmbMacroFilter
{
	const DEFAULT_CLIP_LIMIT = 255;

  function getValue()
  {
  	$value_ptr = $this->base->getValue();
  	$limit = isset($this->params[0]) ? $this->params[0] : self::DEFAULT_CLIP_LIMIT;
  	$end_symbol = isset($this->params[1]) ? $this->params[1] : '';

    $substr = 'substr('.$value_ptr.', 0, '.$this->params[0].')';
    if ($end_symbol)
      $substr .= '.((strlen('.$value_ptr.') > '.$limit.') ? '.$end_symbol.' : "")';

    return $substr;
  }
}
