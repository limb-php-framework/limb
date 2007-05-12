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

class WactContentBlockAnalizerListener implements WactBlockAnalizerListener
{
  protected $tree_builder;
  protected $location;
  protected $filter_dictionary;

  function __construct($tree_builder, $location, $filter_dictionary)
  {
    $this->tree_builder = $tree_builder;
    $this->location = $location;
    $this->filter_dictionary = $filter_dictionary;
  }

  function addLiteralFragment($text)
  {
    $this->tree_builder->addWactTextNode($text);
  }

  function addExpressionFragment($text)
  {
    $expression = new WactExpression($text,
                                     $this->tree_builder->getCursor(),
                                     $this->filter_dictionary, 'html');

    $output_expression = new WactOutputExpressionNode($this->location, $expression);
    $this->tree_builder->addNode($output_expression);
  }
}
?>
