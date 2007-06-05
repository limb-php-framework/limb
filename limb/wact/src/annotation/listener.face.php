<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

class WactClassAnnotationWactParserListener
{
  function annotation($type, $title = NULL){}
  function beginClass($className, $parentName = NULL){}
  function endClass($className){}
  function property($name, $access = 'public'){}
  function method($name){}
}

?>
