<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactHTMLParserListener.interface.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

interface WactHTMLParserListener
{
  function startTag($tag_name, $attrs, $location);
  function endTag($tag_name, $location);
  function emptyTag($tag_name, $attrs, $location);
  function characters($data, $location);
  function instruction($type, $data, $location);
}
?>