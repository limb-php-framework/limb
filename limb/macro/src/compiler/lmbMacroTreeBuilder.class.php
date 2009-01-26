<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Acts on the root lmbMacroNode in response to events within the lmbMacroParser
 *
 * When adding an open tag to the tree, call pushExpectedTag().  When closing
 * a tag, call popExpectedTag(), which ensures the tree is balanced.
 *
 * These methods do not actually add nodes to the tree, as tags and nodes
 * do not necessarily match up.
 *
 * To add a node to the tree, you have the following choices.  To add a node
 * which can have children, use pushNode().  To add a terminal node use addNode(),
 * or addTextNode().
 * @package macro
 * @version $Id$
 */

class lmbMacroTreeBuilder
{
  protected $compiler;
  protected $node;

  protected $filter_dictionary;
  /**
  * Stack of tags pushed onto the tree builder
  */
  var $expected_tags = array();

  function __construct($compiler)
  {
    $this->compiler = $compiler;
  }

  /**
  * Returns the current node
  */
  function getCursor()
  {
    return $this->node;
  }

  /**
  * Sets the cursor (the current working node) of the tree builder
  */
  function setCursor($node)
  {
    $this->node = $node;
  }

  /**
  * Begins a node's build phase in relation to the node tree.
  * Adds a node to the tree, then makes that node the 'cursor'.
  */
  function pushNode($node)
  {
    $this->node->addChild($node);
    $this->setCursor($node);
    return $this->node->preParse($this->compiler);
  }

  /**
  * Adds a node to the tree, without descending into it.
  * This begins and finishes the node's composition
  */
  function addNode($node)
  {
    $node->preParse($this->compiler);
    $this->node->addChild($node);
  }

  function addTextNode($text)
  {
    $this->addNode(new lmbMacroTextNode(null, $text));
  }

  function addContent($text, $location)
  {
    $listener = new lmbMacroContentBlockAnalizerListener($this, $location, $this->filter_dictionary);
    $analizer = new lmbMacroBlockAnalizer();
    $analizer->parse($text, $listener);
  }

  /**
  * Ends a node's build phase in relation to the tree.
  * Checks child server ids and moves the 'cursor' up the tree to the parent
  * node.
  */
  function popNode()
  {
    $this->node->checkChildrenIds();
    $this->setCursor($this->node->getParent());
  }

  function pushExpectedTag($tag, $location = null)
  {
    array_push($this->expected_tags, array($tag, $location));
  }

  /**
  * Sets the cursor to a new position, and pushes the old cursor onto the
  * expected tags stack.
  * @see popExpectedTag
  */
  function pushCursor($newPosition, $location)
  {
    // use of array() is to preserve reference from array_pop()
    array_push($this->expected_tags, array($this->node, $location));
    $this->setCursor($newPosition);
  }

  /**
  * Tests the passed tag against what is expected.  Returns any info that
  * was kept about the expected tag.
  * If the item in the tag stack is a node, then the cursor is
  * restored to that, and popExpectedTag is called again.
  */
  function popExpectedTag($tag, $location)
  {
    if(!$expected_tag_item = array_pop($this->expected_tags))
    {
      throw new lmbMacroException('Lonely closing tag', array('tag' => $tag,
                                                          'file' => $location->getFile(),
                                                          'line' => $location->getLine()));
    }

    // if we have a node on the stack, restore the cursor to that, and
    // pop the stack again
    if(is_object($expected_tag_item[0]))
    {
      $this->node = $expected_tag_item[0];
      return $this->popExpectedTag($tag, $location);
    }

    $expected_tag = $expected_tag_item[0];
    $expected_location = $expected_tag_item[1];

    if(strcasecmp($expected_tag, $tag) === 0)
      return $tag;

    throw new lmbMacroException('Unexpected closing tag',
                             array('file' => $location->getFile(),
                                  'tag' => $tag,
                                  'line' => $location->getLine(),
                                  'expected_tag' => $expected_tag,
                                  'expected_file' => $expected_location->getFile(),
                                  'expected_line' => $expected_location->getLine()));
  }

  /**
  * Return the size of the expected tags stack
  */
  function getExpectedTagCount()
  {
    return count($this->expected_tags);
  }

  /**
  * Returns the current expected tag
  */
  function getExpectedTag()
  {
    // Returns the tagname of the first non-node item on the stack
    $item = end($this->expected_tags);
    while($item && !is_string($item[0]))
      $item = prev($this->expected_tags);
    return $item ? $item[0] : false;
  }

  function getExpectedTagLocation()
  {
    $item = end($this->expected_tags);
    return $item[1];
  }

  function getFilterDictionary()
  {
    return $this->compiler->getFilterDictionary();
  }
}

