<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactParserListener.interface.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

interface WactParserListener
{
  function startElement($tag_name, $attrs);
  function endElement($tag_name);
  function emptyElement($tag_name, $attrs);
  function characters($data);
  function cdata($data);
  function processingInstruction($type, $data);
  function escape($data);
  function comment($data);
  function doctype($data);
  function jasp($data);
  function unexpectedEOF($data);
  function invalidEntitySyntax($data);
  function invalidAttributeSyntax();
  function setDocumentLocator($locator);
}
?>