<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * interface WactHTMLParserListener.
 *
 * @package wact
 * @version $Id: WactHTMLParserListener.interface.php 7486 2009-01-26 19:13:20Z pachanga $
 */
interface WactHTMLParserListener
{
  function startTag($tag_name, $attrs, $location);
  function endTag($tag_name, $location);
  function emptyTag($tag_name, $attrs, $location);
  function characters($data, $location);
  function instruction($type, $data, $location);
}

