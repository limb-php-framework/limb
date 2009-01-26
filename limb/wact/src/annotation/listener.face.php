<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactClassAnnotationWactParserListener.
 *
 * @package wact
 * @version $Id: listener.face.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class WactClassAnnotationWactParserListener
{
  function annotation($type, $title = NULL){}
  function beginClass($className, $parentName = NULL){}
  function endClass($className){}
  function property($name, $access = 'public'){}
  function method($name){}
}


