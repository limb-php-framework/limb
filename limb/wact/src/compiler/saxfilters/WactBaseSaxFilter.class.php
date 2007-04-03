<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactBaseSaxFilter.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

class WactBaseSaxFilter
{
  protected $decorated;

  function __construct($decorated)
  {
    $this->decorated = $decorated;
  }

  function setDocumentLocator($locator)
  {
    $this->decorated->setDocumentLocator($locator);
  }

  function startElement($tag, $attrs)
  {
    $this->decorated->startElement($tag, $attrs);
  }

  function endElement($tag)
  {
    $this->decorated->endElement($tag);
  }

  function emptyElement($tag, $attrs)
  {
    $this->decorated->emptyElement($tag, $attrs);
  }

  function characters($text)
  {
    $this->decorated->characters($text);
  }

  function cdata($text)
  {
    $this->decorated->cdata($text);
  }

  function processingInstruction($target, $instruction)
  {
    $this->decorated->processingInstruction($target, $instruction);
  }

  function escape($text)
  {
    $this->decorated->escape($text);
  }

  function comment($text)
  {
    $this->decorated->comment($text);
  }

  function doctype($text)
  {
    $this->decorated->doctype($text);
  }

  function jasp($text)
  {
    $this->decorated->jasp($text);
  }

  function unexpectedEOF($text)
  {
    $this->decorated->unexpectedEOF($text);
  }

  function invalidEntitySyntax($text)
  {
    $this->decorated->invalidEntitySyntax($text);
  }

  function invalidAttributeSyntax()
  {
    $this->decorated->invalidAttributeSyntax();
  }
}
?>