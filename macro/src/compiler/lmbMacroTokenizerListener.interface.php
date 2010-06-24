<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * interface lmbMacroTokenizerListener.
 *
 * @package macro
 * @version $Id$
 */
interface lmbMacroTokenizerListener
{
  function startElement($tag_name, $attrs);
  function endElement($tag_name);
  function emptyElement($tag_name, $attrs);
  function characters($data);
  function php($data);
  function unexpectedEOF($data);
  function invalidEntitySyntax($data);
  function invalidAttributeSyntax($data);
}

