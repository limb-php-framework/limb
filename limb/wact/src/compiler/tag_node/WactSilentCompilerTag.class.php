<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Silent compiler directive tags are instructions for the compiler and do
 * not have a corresponding WactRuntimeComponent, nor do they normally generate
 * output into the compiled template.
 * @package wact
 * @version $Id: WactSilentCompilerTag.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactSilentCompilerTag extends WactCompilerTag
{
  function generate($code_writer)
  {
    // Silent Compiler Directives do not generate their contents during the
    // normal generation sequence.
  }

  function generateNow($code_writer)
  {
    return parent :: generate($code_writer);
  }
}

