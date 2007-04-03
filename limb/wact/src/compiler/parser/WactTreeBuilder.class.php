<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactTreeBuilder.class.php 5203 2007-03-07 08:58:21Z serega $
 * @package    wact
 */

/**
* Acts on the WactCompileTreeRootNode in response to events within the WactSourceFileParser
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
class WactTreeBuilder
{
    /**
    * @var WactCompiler
    */
    protected $compiler;

    /**
    * Current node
    * @var WactCompileTreeNode
    * @access private
    */
    var $component;

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
    * @return WactCompileTreeNode
    * @access public
    */
    function getCursor()
    {
      return $this->component;
    }

  /**
  * Sets the cursor (the current working component) of the tree builder
  * @param WactCompileTreeNode
  * @return void
  * @access public
  */
    function setCursor($component)
    {
      $this->component = $component;
    }

  /**
  * Begins a component's build phase in relation to the component tree.
  * Adds a component to the tree, then makes that component the 'cursor'.
  * @param WactCompileTreeNode
  * @return void
  * @access public
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
  * @param WactCompileTreeNode
  */
  function addNode($childComponent)
  {
    $childComponent->preParse($this->compiler);
    $this->component->addChild($childComponent);
  }

  function addWactTextNode($text)
  {
    $this->addNode(new WactTextNode(null, $text));
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
  * @param string tag name
  * @param mixed info about the tag
  * @return void
  * @access public
  */
  function pushExpectedTag($tag, $info = null, $location = null)
  {
    array_push($this->expectedTags, array($tag, $info, $location));
  }

  /**
  * Sets the cursor to a new position, and pushes the old cursor onto the
  * expected tags stack.
  * @see popExpectedTag
  * @param WactCompileTreeNode
  * @return void
  * @access public
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
      throw new WactException('Lonely closing tag', array('tag' => $tag,
                                                          'file' => $location->getFile(),
                                                          'line' => $location->getLine()));
    }

    // if we have a component on the stack, restore the cursor to that, and
    // pop the stack again
    if (is_object($expectedTagItem[0]))
    {
       $this->component =& $expectedTagItem[0];
       return $this->popExpectedTag($tag, $location);
    }

    $expectedTag = $expectedTagItem[0];
    $info = $expectedTagItem[1];

    if (strcasecmp($expectedTag, $tag) !== 0)
    {
      throw new WactException('Unexpected closing tag',
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
  function getExpectedTagCount() {
    return count($this->expectedTags);
  }

  /**
  * Returns the current expected tag
  */
  function getExpectedTag()
  {
    // Returns the tagname of the first non-component item on the stack
    $item = end($this->expectedTags);
    while ($item && !is_string($item[0])) {
      $item = prev($this->expectedTags);
    }
    return $item ? $item[0] : false;
  }

  function getExpectedTagLocation()
  {
    $item = end($this->expectedTags);
    return $item[2];
  }

}
?>
