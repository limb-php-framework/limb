<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactNodeBuilder.class.php 5660 2007-04-13 20:29:02Z serega $
 * @package    wact
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
?>
