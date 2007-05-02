<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    macro
 */

/**
* Acts on the lmbMacroTreeRootNode in response to events within the lmbMacroSourceFileParser
*
* When adding an open tag to the tree, call pushExpectedTag().  When closing
* a tag, call popExpectedTag(), which ensures the tree is balanced.
*
* These methods do not actually add nodes to the tree, as tags and nodes
* do not necessarily match up.
*
* To add a node to the tree, you have the following choices.  To add a node
* which can have children, use pushNode().  To add a terminal node use addNode(),
* or addWactTextNode().
*
*/
class lmbMacroTreeBuilder
{
    protected $compiler;
    protected $component;

    /**
    * Stack of tags pushed onto the tree builder, may also contain components
    * @see pushExpectedTag
    * @see popExpectedTag
    * @see pushCursor
    * @var array of array($tagname, $info) or array($Component)
    * @access private
    */
    var $expectedTags = array();

    function __construct($compiler)
    {
      $this->compiler = $compiler;
    }

    /**
    * Returns the current component
    */
    function getCursor()
    {
      return $this->component;
    }

    /**
    * Sets the cursor (the current working component) of the tree builder
    */
    function setCursor($component)
    {
      $this->component = $component;
    }

    /**
    * Begins a component's build phase in relation to the component tree.
    * Adds a component to the tree, then makes that component the 'cursor'.
    */
    function pushNode($newComponent)
    {
      $this->component->addChild($newComponent);
      $this->setCursor($newComponent);
      return $this->component->preParse($this->compiler);
    }

    /**
    * Adds a component to the tree, without descending into it.
    * This begins and finishes the component's composition
    */
    function addNode($childComponent)
    {
      $childComponent->preParse($this->compiler);
      $this->component->addChild($childComponent);
    }

    function addTextNode($text)
    {
      $this->addNode(new lmbMacroTextNode(null, $text));
    }

    /**
    * Ends a component's build phase in relation to the tree.
    * Checks child server ids and moves the 'cursor' up the tree to the parent
    * component.
    */
    function popNode($hasClosingTag)
    {
      $this->component->hasClosingTag = $hasClosingTag;
      $this->component->checkChildrenServerIds();
      $this->setCursor($this->component->parent);
    }

    /**
    * Expects the passed tag.  Optionally $info may be passed which is info
    * about that tag.  The parser state that calls TreeBuilder may use this info
    * to differentiate, say, plain vs. component tags.
    */
    function pushExpectedTag($tag, $info = null, $location = null)
    {
      array_push($this->expectedTags, array($tag, $info, $location));
    }

    /**
    * Sets the cursor to a new position, and pushes the old cursor onto the
    * expected tags stack.
    * @see popExpectedTag
    */
    function pushCursor($newPosition, $location)
    {
      // use of array() is to preserve reference from array_pop()
      array_push($this->expectedTags, array($this->component, PARSER_TAG_IS_COMPONENT, $location));
      $this->setCursor($newPosition);
    }

    /**
    * Tests the passed tag against what is expected.  Returns any info that
    * was kept about the expected tag.
    * If the item in the tag stack is a component, then the cursor is
    * restored to that, and popExpectedTag is called again.
    */
    function popExpectedTag($tag, $location)
    {
      if(!$expectedTagItem = array_pop($this->expectedTags))
      {
        throw new lmbMacroException('Lonely closing tag', array('tag' => $tag,
                                                            'file' => $location->getFile(),
                                                            'line' => $location->getLine()));
      }

      // if we have a component on the stack, restore the cursor to that, and
      // pop the stack again
      if(is_object($expectedTagItem[0]))
      {
        $this->component =& $expectedTagItem[0];
        return $this->popExpectedTag($tag, $location);
      }

      $expectedTag = $expectedTagItem[0];
      $info = $expectedTagItem[1];

      if(strcasecmp($expectedTag, $tag) !== 0)
      {
        throw new lmbMacroException('Unexpected closing tag',
                                 array('file' => $location->getFile(),
                                      'tag' => $tag,
                                      'line' => $location->getLine(),
                                      'ExpectTag' => $expectedTag,
                                      'ExpectTagFile' => $expectedTagItem[2]->getFile(),
                                      'ExpectedTagLine' => $expectedTagItem[2]->getLine()));
      }
      return $info;
    }

  /**
  * Return the size of the expected tags stack
  */
  function getExpectedTagCount()
  {
    return count($this->expectedTags);
  }

  /**
  * Returns the current expected tag
  */
  function getExpectedTag()
  {
    // Returns the tagname of the first non-component item on the stack
    $item = end($this->expectedTags);
    while($item && !is_string($item[0]))
      $item = prev($this->expectedTags);
    return $item ? $item[0] : false;
  }

  function getExpectedTagLocation()
  {
    $item = end($this->expectedTags);
    return $item[2];
  }
}
?>
