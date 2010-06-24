<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
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
 * @package wact
 * @version $Id: WactTreeBuilder.class.php 7686 2009-03-04 19:57:12Z korchasa $
 */


define ('WACT_EXPECTED_WACT_TAG', 	1);
define ('WACT_EXPECTED_PLAIN_TAG',	2);class WactTreeBuilder
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

  protected $property_dictionary;
  protected $tag_dictionary;
  protected $filter_dictionary;

  /**
  * Stack of tags pushed onto the tree builder, may also contain components
  * @see pushExpectedTag
  * @see popExpectedTag
  * @see pushCursor
  * @access private
  */
  protected $expected_tags = array();

  function __construct($compiler, $tag_dictionary, $property_dictionary, $filter_dictionary)
  {
    $this->compiler = $compiler;
    $this->tag_dictionary = $tag_dictionary;
    $this->property_dictionary = $property_dictionary;
    $this->filter_dictionary = $filter_dictionary;
  }

  /**
  * Returns the current component
  * @return WactCompileTreeNode
  * @return void
  */
  function getCursor()
  {
    return $this->cursor_node;
  }

  /**
  * Sets the cursor (the current working component) of the tree builder
  * @param WactCompileTreeNode
  * @return void
  */
  function setCursor($node)
  {
    $this->cursor_node = $node;
  }

  /**
  * Begins a component's build phase in relation to the component tree.
  * Adds a component to the tree, then makes that component the 'cursor'.
  * @param WactCompileTreeNode
  * @return void
  */
  function pushNode($new_node)
  {
    $this->cursor_node->addChild($new_node);
    $this->setCursor($new_node);

    return $this->cursor_node->preParse($this->compiler);
  }

  /**
  * Adds a component to the tree, without descending into it.
  * This begins and finishes the component's composition
  * @param WactCompileTreeNode
  */
  function addNode($child_node)
  {
    $this->cursor_node->addChild($child_node);
    $child_node->preParse($this->compiler);
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
  function popNode()
  {
    $this->cursor_node->checkChildrenServerIds();
    $this->setCursor($this->cursor_node->parent);
  }

  /**
  * @param string tag name
  * @param mixed info about the tag
  * @param boolean if the tag is one of the wact tags (of having wact:id or runat='server' attributes)
  * @return void
  */
  function _pushExpectedTag($tag, $location, $tag_kind)
  {
    array_push($this->expected_tags, array($tag, $location, $tag_kind));
  }

  function pushExpectedPlainTag($tag, $location)
  {
    $this->_pushExpectedTag($tag, $location, WACT_EXPECTED_PLAIN_TAG);
  }

  function pushExpectedWactTag($tag, $location)
  {
    $this->_pushExpectedTag($tag, $location, WACT_EXPECTED_WACT_TAG);
  }

  /**
  * Sets the cursor to a new position, and pushes the old cursor onto the expected tags stack.
  * @param WactCompileTreeNode
  * @return void
  */
  function pushCursor($new_position, $location)
  {
    $this->pushExpectedWactTag($this->cursor_node, $location);
    $this->setCursor($new_position);
  }

  /**
  * Tests the passed tag against what is expected.
  * Returns info if expected tag was a wact tag or a plain tag
  */
  protected function _popExpectedTag($pop_tag, $pop_tag_location, $pop_tag_kind)
  {
    if(!$expected_tag_info = array_pop($this->expected_tags))
    {
      throw new WactException('Lonely closing tag', array('tag' => $pop_tag,
                                                          'file' => $pop_tag_location->getFile(),
                                                          'line' => $pop_tag_location->getLine()));
    }

    // if we have a cursor on the stack, restore the current cursor to that, and
    // pop the stack again
    if (is_object($expected_tag_info[0]))
    {
       $this->cursor_node =& $expected_tag_info[0];
       return $this->_popExpectedTag($pop_tag, $pop_tag_location, $pop_tag_kind);
    }

    $expected_tag = $expected_tag_info[0];
    $expected_tag_location = $expected_tag_info[1];
    $expected_tag_kind = $expected_tag_info[2];

    // if the tag names are equal we dont care about if they are both wact tags or not
    if(strcasecmp($expected_tag, $pop_tag) === 0)
      return $expected_tag_kind;

    // ignore plain html tag since we dont need to balance them
    if($expected_tag_kind == WACT_EXPECTED_PLAIN_TAG)
      return $this->_popExpectedTag($pop_tag, $pop_tag_location, $pop_tag_kind);

    // we stops at the nearest wact tag if we poping up a plain html tag
    if(($expected_tag_kind == WACT_EXPECTED_WACT_TAG) && ($pop_tag_kind == WACT_EXPECTED_PLAIN_TAG))
    {
      // restore stack
      $this->pushExpectedWactTag($expected_tag, $expected_tag_location);
      return WACT_EXPECTED_PLAIN_TAG;
    }

    // if we expected a wact tag and poping up a wact tag also and the tags names are different
    //  => template is not balanced.
    throw new WactException('Unexpected closing tag',
                            array('file' => $pop_tag_location->getFile(),
                                  'tag' => $pop_tag,
                                  'line' => $pop_tag_location->getLine(),
                                  'ExpectTag' => $expected_tag,
                                  'ExpectTagFile' => $expected_tag_location->getFile(),
                                  'ExpectedTagLine' => $expected_tag_location->getLine()));
  }

  function popExpectedPlainTag($pop_tag, $pop_tag_location)
  {
    return $this->_popExpectedTag($pop_tag, $pop_tag_location, WACT_EXPECTED_PLAIN_TAG);
  }

  function popExpectedWactTag($pop_tag, $pop_tag_location)
  {
    return $this->_popExpectedTag($pop_tag, $pop_tag_location, WACT_EXPECTED_WACT_TAG);
  }

  function getExpectedTagCount()
  {
    return count($this->expected_tags);
  }

  function getExpectedTag()
  {
    // Returns the tagname of the first non-component item on the stack
    $item = end($this->expected_tags);
    while ($item && !is_string($item[0])) {
      $item = prev($this->expected_tags);
    }
    return $item ? $item[0] : false;
  }

  function getExpectedTagLocation()
  {
    $item = end($this->expected_tags);
    return $item[1];
  }

  function addProcessingInstruction($target, $instruction)
  {
    // Pass through any PI's except PHP PI's
    $php_targets = array('php','PHP','=','');
    if(in_array($target, $php_targets))
    {
      $this->addNode(new WactPHPNode(null, $instruction));
    }
    else
    {
      $php = 'echo "<?'.$target.' '; // Whitespace assumption
      $php.= str_replace('"','\"',$instruction);
      $php.= '?>\n";'; // Newline assumption
      $this->addNode(new WactPHPNode(null, $php));
    }
  }

  /**
  * Builds a component, adding attributes
  * @param WactTagInfo
  * @param string XML tag name of component
  * @param array attributes for tag
  * @param boolean whether the tag has contents
  * @return WactCompileTreeNode
  */
  function buildTagNode($tag_info, $tag, $location, $attrs, $is_empty = false, $has_closing_tag = true)
  {
    $tag_node = $this->_createTagNode($tag_info, $tag, $location);

    $this->_registerPropertiesInTagNode($tag_node);

    $tag_node->emptyClosedTag = $is_empty;
    $tag_node->hasClosingTag = $has_closing_tag;

    $this->_convertAttributesToExpressionIfRequired($tag_node, $tag_info, $attrs);

    $this->_addAttributesToTagNode($tag_node, $location, $attrs);

    return $tag_node;
  }

  protected function _createTagNode($tag_info, $tag, $location)
  {
    $class = $tag_info->TagClass;
    $tag_node = new $class($location, $tag, $tag_info);

    return $tag_node;
  }

  protected function _registerPropertiesInTagNode($tag_node)
  {
    $properties = $this->property_dictionary->getPropertyList($tag_node);
    foreach ($properties as $property)
    {
      $property->load();
      $property_class = $property->PropertyClass;
      $tag_node->registerProperty($property->Property, new $property_class($tag_node));
    }
  }

  protected function _addAttributesToTagNode($tag_node, $location, $attrs)
  {
    foreach ($attrs as $name => $value)
    {
      $attribute = new WactAttribute($name);
      if($value !== NULL)
      {
        $listener = new WactAttributeBlockAnalizerListener($attribute, $tag_node, $this->filter_dictionary);

        $analizer = new WactBlockAnalizer();
        $analizer->parse($value, $listener);
      }

      $tag_node->addChildAttribute($attribute);
    }
  }

  // this code added to support old form of DBE expressions in some attributes
  // like <core:optional for='var'> should actually be <core:optional for={$ var }>
  protected function _convertAttributesToExpressionIfRequired($tag_node, $tag_info, &$attributes)
  {
    foreach($tag_info->getConvertAttributesToExpressions() as $name)
    {
      if(!isset($attributes[$name]))
        continue;

      $value = $attributes[$name];

      if(strpos($value, '{$') !== FALSE)
        continue;


      if($tag_node->isDataSource() && (strpos($value, '#') === FALSE) && strpos($value, '$') === FALSE)
        $attributes[$name] = '{$^' . $value . '}';
      else
        $attributes[$name] = '{$' . $value . '}';
    }
  }

  function addContent($text, $location)
  {
    $listener = new WactContentBlockAnalizerListener($this, $location, $this->filter_dictionary);
    $analizer = new WactBlockAnalizer();
    $analizer->parse($text, $listener);
  }
}

