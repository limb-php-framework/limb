<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactClassAnnotationWactParserListener.
 *
 * @package wact
 * @version $Id: listener.face.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class WactClassAnnotationWactParserListener
{
  function annotation($type, $title = NULL){}
  function beginClass($className, $parentName = NULL){}
  function endClass($className){}
  function property($name, $access = 'public'){}
  function method($name){}
}


