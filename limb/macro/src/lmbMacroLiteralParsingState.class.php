<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/macro/src/lmbMacroTokenizerListener.interface.php');
require_once('limb/macro/src/lmbMacroBaseParsingState.class.php');

/**
 * class lmbMacroLiteralParsingState.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroLiteralParsingState extends lmbMacroBaseParsingState  implements lmbMacroTokenizerListener
{
  protected $literal_tag;

  function setLiteralElement($tag)
  {
    $this->literal_tag = $tag;
  }

  function getLiteralElement()
  {
    return $this->literal_tag;
  }

  function startElement($tag, $attrs)
  {
    $location = $this->locator->getCurrentLocation();
    $this->tree_builder->addTextNode('<%' . $tag . $this->getAttributeString($attrs) . '%>');
  }

  function endElement($tag)
  {
    $location = $this->locator->getCurrentLocation();
    if ($this->literal_tag == $tag)
      $this->_closeLiteralState($location);
    else
      $this->tree_builder->addTextNode('<%/' . $tag . '%>');
  }

  protected function _closeLiteralState($location)
  {
    $this->tree_builder->popExpectedlmbMacroElement($this->literal_tag, $location);
    $this->tree_builder->popNode();
    $this->parser->changeToComponentParsingState();
  }

  function emptyElement($tag, $attrs)
  {
    $this->tree_builder->addTextNode('<%' . $tag . $this->getAttributeString($attrs) . ' /%>');
  }

  function characters($text)
  {
    $this->tree_builder->addTextNode($text);
  }

  function unexpectedEOF($text)
  {
    $this->tree_builder->addTextNode($text);
  }

  function invalidEntitySyntax($text)
  {
    $this->tree_builder->addTextNode($text);
  }
}

