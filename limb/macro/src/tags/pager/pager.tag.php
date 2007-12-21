<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @tag pager
 * @package macro
 * @version $Id$
 */
class lmbMacroPagerTag extends lmbMacroRuntimeWidgetTag
{
  protected $widget_include_file = 'limb/macro/src/tags/pager/lmbMacroPagerHelper.class.php';
  protected $widget_class_name = 'lmbMacroPagerHelper';
  
  protected function _generateContent($code)
  {
    $pager = $this->getRuntimeVar();
    
    $code->writePhp("{$pager}->prepare();\n");
    
    $this->_generatePagerVariables($code, $pager);
    
    parent :: _generateContent($code);
  }
  
  protected function _generateWidget($code)
  {
    parent :: _generateWidget($code);
    
    $pager = $this->getRuntimeVar();

    if ($total_items = $this->getEscaped('total_items'))
      $code->writeToInit("{$pager}->setTotalItems({$total_items});\n");
    
    if ($items = $this->getEscaped('items'))
      $code->writeToInit("{$pager}->setItemsPerPage({$items});\n");

    if($this->findChildByClass('lmbMacroPagerElipsesTag'))
    {
      $code->writeToInit("{$pager}->useElipses();\n");

      if ($this->has('pages_in_middle'))
      {
        $pages_in_middle = $this->getEscaped('pages_in_middle');
        $code->writeToInit("{$pager}->setPagesInMiddle({$pages_in_middle});\n");
      }

      if ($this->has('pages_in_sides'))
      {
        $pages_in_sides = $this->getEscaped('pages_in_sides');
        $code->writeToInit("{$pager}->setPagesInSides((int){$pages_in_sides});\n");
      }
    }
    else
    {
      $code->writeToInit("{$pager}->useSections();\n");
      
      if ($pages_per_section = $this->getEscaped('pages_per_section'))
        $code->writeToInit("{$pager}->setPagesPerSection({$pages_per_section});\n");
    }
  }
  
  protected function _generatePagerVariables($code, $pager)
  {
     $code->writePhp("\$total_items = {$pager}->getTotalItems();\n");
     $code->writePhp("\$total_pages = {$pager}->getTotalPages();\n");
     $code->writePhp("\$items_per_page = {$pager}->getItemsPerPage();\n");
     $code->writePhp("\$begin_item_number = {$pager}->getCurrentPageBeginItem();\n");
     $code->writePhp("\$end_item_number = {$pager}->getCurrentPageEndItem();\n");
  }
}


