<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactSilentCompilerTag.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
* Silent compiler directive tags are instructions for the compiler and do
* not have a corresponding WactRuntimeComponent, nor do they normally generate
* output into the compiled template.
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
?>