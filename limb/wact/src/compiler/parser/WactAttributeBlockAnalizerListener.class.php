<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactAttributeBlockAnalizerListener.
 *
 * @package wact
 * @version $Id$
 */
class WactAttributeBlockAnalizerListener implements WactBlockAnalizerListener
{
  protected $attribute;
  protected $tag_node;
  protected $filter_dictionary;

  function __construct($attribute, $tag_node, $filter_dictionary)
  {
    $this->attribute = $attribute;
    $this->tag_node = $tag_node;
    $this->filter_dictionary = $filter_dictionary;
  }

  function addLiteralFragment($text)
  {
    $fragment = new WactAttributeLiteralFragment($text);
    $this->attribute->addFragment($fragment);
  }

  function addExpressionFragment($text)
  {
    $fragment = new WactAttributeExpressionFragment($text,
                                                    $this->tag_node,
                                                    $this->filter_dictionary);

    $this->attribute->addFragment($fragment);
  }
}

