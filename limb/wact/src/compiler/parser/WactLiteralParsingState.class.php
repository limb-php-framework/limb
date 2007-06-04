<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactLiteralParsingState.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

require_once('limb/wact/src/compiler/parser/WactHTMLParserListener.interface.php');
require_once('limb/wact/src/compiler/parser/WactBaseParsingState.class.php');

class WactLiteralParsingState extends WactBaseParsingState  implements WactHTMLParserListener
{
  protected $literal_tag;

  function setLiteralTag($tag)
  {
    $this->literal_tag = $tag;
  }

  function getLiteralTag()
  {
    return $this->literal_tag;
  }

  function startTag($tag, $attrs, $location)
  {
    $this->tree_builder->addWactTextNode('<' . $tag . $this->getAttributeString($attrs) . '>');
  }

  function endTag($tag, $location)
  {
    if ($this->literal_tag == $tag)
      $this->_closeLiteralState($location);
    else
      $this->tree_builder->addWactTextNode('</' . $tag . '>');
  }

  protected function _closeLiteralState($location)
  {
    $this->tree_builder->popExpectedWactTag($this->literal_tag, $location);
    $this->tree_builder->popNode();
    $this->parser->changeToComponentParsingState();
  }

  function emptyTag($tag, $attrs, $location)
  {
    $this->tree_builder->addWactTextNode('<' . $tag . $this->getAttributeString($attrs) . ' />');
  }

  function characters($text, $location)
  {
    $this->tree_builder->addWactTextNode($text);
  }

  function instruction($target, $instruction, $location)
  {
    $this->tree_builder->addWactTextNode('<?' . $target . ' ' . $instruction . '?>');
  }
}
?>