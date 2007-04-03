<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactNodeBuilder.class.php 5223 2007-03-13 13:31:39Z serega $
 * @package    wact
 */

/**
* Responsible for building nodes of the component tree.
* WactTreeBuilder is responsible for the tree structure.
*/
class WactNodeBuilder
{
  const BEFORE_CONTENT = 1;
  const EXPRESSION = 2;
  const AFTER_CONTENT = 5;

  protected $variable_reference_pattern;

  protected $locator;

  /**
  * @var WactFilterDictionary
  */
  protected $filter_dictionary;

  /**
  * @var WactPropertyDictionary
  */
  protected $property_dictionary;


  function __construct($tree_builder, $property_dictionary, $filter_dictionary)
  {
    $this->tree_builder = $tree_builder;
    $this->variable_reference_pattern = $this->_getVariableReferentPattern();
    $this->property_dictionary = $property_dictionary;
    $this->filter_dictionary = $filter_dictionary;
  }

  function setDocumentLocator($locator)
  {
    $this->locator = $locator;
  }

  protected function _getVariableReferentPattern()
  {
    $pattern =
      // start at the beginning
      '/^' .
      // Pick up the portion of the string before the variable reference
      '((?s).*?)' .
      // Beginning of a variable reference
      preg_quote('{$', '/') .
      // Collect the entire variable reference into one subexpression
      '(' .
          // capture the contents of one or more fragments.
          '(' .
              // Anything thats not a quote or the end of the variable
              // reference can be in a fragment
              '[^"\'}]+' .
              // OR
              '|' .
              // A string inside quotes is also a fragment
              '(\'|").*?\4' .
          ')+' .
      ')' .
      // end of a variable reference
      preg_quote('}', '/') .
      // Pick up the portion of the string after the variable reference
      // This portion may contain additional references; we only match
      // one at a time.
      '((?s).*)' .
      // Match until the end of the string
      '$/';

    return $pattern;
  }

  /**
  * Builds a component, adding attributes
  * @param WactTagInfo
  * @param string XML tag name of component
  * @param array attributes for tag
  * @param boolean whether the tag has contents
  * @return WactCompileTreeNode
  */
  function buildTagNode($tag_info, $tag, $attrs, $isEmpty)
  {
    $tag_node = $this->_createTagNode($tag_info, $tag);
    $tag_node->emptyClosedTag = $isEmpty;
    $this->_addAttributesToTagNode($tag_node, $attrs);
    return $tag_node;
  }

  /**
  * Builds content node(s), adding it (them) to the component tree.
  * A single piece of content may actually be a mix of terminal nodes
  * (WactTextNodes and WactExpressions)
  */
  function addContent($text)
  {
    // if there is no expression (common case), shortcut this process
    if (strpos($text, '{$') === FALSE)
    {
      $this->tree_builder->addWactTextNode($text);
      return;
    }
    $location = $this->locator->getCurrentLocation();

    while (preg_match($this->variable_reference_pattern, $text, $match))
    {
      if (strlen($match[self :: BEFORE_CONTENT]) > 0)
          $this->tree_builder->addWactTextNode($match[self :: BEFORE_CONTENT]);

      $expression = new WactExpression($match[self :: EXPRESSION],
                                       $this->tree_builder->getCursor(),
                                       $this->filter_dictionary, 'html');

      $output_expression = new WactOutputExpressionNode($location, $expression);
      $this->tree_builder->addNode($output_expression);

      $text = $match[self :: AFTER_CONTENT];
    }

    if (strlen($text) > 0)
      $this->tree_builder->addWactTextNode($text);
  }

  function addProcessingInstruction($target, $instruction)
  {
    // Pass through any PI's except PHP PI's
    $php_targets = array('php','PHP','=','');
    if(in_array($target, $php_targets))
    {
      $this->tree_builder->addNode(new WactPHPNode(null, $instruction));
    }
    else
    {
      $php = 'echo "<?'.$target.' '; // Whitespace assumption
      $php.= str_replace('"','\"',$instruction);
      $php.= '?>\n";'; // Newline assumption
      $this->tree_builder->addNode(new WactPHPNode(null, $php));
    }
  }

  protected function _createTagNode($tag_info, $tag)
  {
    $class = $tag_info->TagClass;
    $tag_node = new $class($this->locator->getCurrentLocation(), $tag, $tag_info);

    $this->_registerPropertiesInTagNode($tag_node);
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

  protected function _addAttributesToTagNode($tag_node, $attrs)
  {
    foreach ($attrs as $name => $value)
    {
      if(($value === NULL) && WACT_STRICT_MODE)
      {
        $location = $this->locator->getCurrentLocation();
        throw new WactException('Attribute should have a value',
                              array('file' => $location->getFile(),
                                    'line' => $location->getLine(),
                                    'tag' => $tag_node->getTag(),
                                    'attribute' => $name));
      }

      // if there is no expression (common case), shortcut this process
      if (strpos($value, '{$') === FALSE)
        $attribute = new WactAttributeNode($name, $value);
      else
      {
        if (preg_match($this->variable_reference_pattern, $value, $match))
        {
          if (strlen($match[self :: AFTER_CONTENT]) == 0 && strlen($match[self :: BEFORE_CONTENT]) == 0)
            $attribute = new WactAttributeExpression($name, $match[self :: EXPRESSION], $tag_node, $this->filter_dictionary);
          else
            $attribute = $this->_createWactCompoundAttribute($tag_node, $name, $value);
        }
        else
          $attribute = new WactAttributeNode($name, $value);
      }

      $tag_node->addChildAttribute($attribute);
    }
  }

  protected function _createWactCompoundAttribute($tag_node, $name, $value)
  {
    $attribute = new WactCompoundAttribute($name);

    while (preg_match($this->variable_reference_pattern, $value, $match))
    {
      if (strlen($match[self :: BEFORE_CONTENT]) > 0)
        $attribute->addAttributeFragment(new WactAttributeNode($name, $match[self :: BEFORE_CONTENT]));

      $expression = new WactAttributeExpression($name,
                                                $match[self :: EXPRESSION],
                                                $tag_node,
                                                $this->filter_dictionary);
      $attribute->addAttributeFragment($expression);

      $value = $match[self :: AFTER_CONTENT];
    }
    if (strlen($value) > 0)
        $attribute->addAttributeFragment(new WactAttributeNode($name, $value));

    return $attribute;
  }
}
?>