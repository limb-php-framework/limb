<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    js
 */

class lmbJsDependencyExtractor
{
  var $base_dir;

  function __construct($base_dir)
  {
    $this->base_dir = $base_dir;
  }

  function getRegex()
  {
    return "~^Limb\.require\(('|\")([^'\"]+)('|\")\)\s*;?~";
  }

  function extractDependency($matches)
  {
    return $this->base_dir . '/' . str_replace('.', '/', $matches[2]) . '.js';
  }
}

?>
