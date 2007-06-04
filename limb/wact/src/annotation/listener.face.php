<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: listener.face.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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
