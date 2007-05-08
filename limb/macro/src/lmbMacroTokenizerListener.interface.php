<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactParserListener.interface.php 5553 2007-04-06 09:05:17Z serega $
 * @package    view
 */

interface lmbMacroTokenizerListener
{
  function startElement($tag_name, $attrs);
  function endElement($tag_name);
  function emptyElement($tag_name, $attrs);
  function characters($data);
  function unexpectedEOF($data);
  function invalidEntitySyntax($data);
  function invalidAttributeSyntax();
  function setTemplateLocator($locator);
}
?>