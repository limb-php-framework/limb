<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * interface lmbMacroBlockAnalizerListener.
 *
 * @package macro
 * @version $Id$
 */
interface lmbMacroBlockAnalizerListener
{
  function addLiteralFragment($text);
  function addExpressionFragment($text);
}

