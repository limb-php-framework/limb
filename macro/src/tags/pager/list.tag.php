<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @tag pager:LIST
 * @restrict_self_nesting
 * @parent_tag_class lmbMacroPagerTag
 * @package macro
 * @version $Id: list.tag.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbMacroPagerListTag extends lmbMacroTag
{
  protected function _generateContent($code)
  {
    $this->pager = $this->findParentByClass('lmbMacroPagerTag')->getRuntimeVar();

    $this->elipses_count_var = $code->generateVar();
    $code->writePhp("{$this->elipses_count_var} = 0;\n");

    $this->show_separator_var = $code->generateVar();
    $code->writePhp("{$this->show_separator_var} = false;\n");
    
    $parent = $this->findParentByClass('lmbMacroPagerTag');
    $code->writePhp("while ({$this->pager}->isValid()) {\n");

    if($this->findChildByClass('lmbMacroPagerElipsesTag'))
      $this->_generateForElipsesMode($code);
    else
      $this->_generateForSectionsMode($code);
      
    $code->writePhp("}\n");
  }

  protected function _generateForSectionsMode($code)
  {
    $code->writePhp("if ({$this->pager}->isDisplayedSection()) {\n");
    
      $this->_generateNumber($code);
      $code->writePhp("{$this->pager}->nextPage();\n");
      $this->_generateSeparator($code);
      
    $code->writePhp("}\n");
    
    $code->writePhp("else {\n");
    
      $this->_generateSection($code);
      $code->writePhp("{$this->pager}->nextSection();\n");
      
    $code->writePhp("}\n");
  }

  protected function _generateForElipsesMode($code)
  {
    $elipses_tag = $this->findChildByClass('lmbMacroPagerElipsesTag');

    if ($separator_tag = $this->findChildByClass('lmbMacroPagerSeparatorTag'))
    {
      $code->writePhp("if ({$this->show_separator_var} && {$this->pager}->shouldDisplayPage()){\n");
        $separator_tag->generateNow($code);
      $code->writePhp("}\n");
      $code->writePhp("{$this->show_separator_var} = true;\n");
    }

    $code->writePhp("if ({$this->pager}->shouldDisplayPage()){\n");
      $this->_generateNumber($code);
      $code->writePhp("{$this->elipses_count_var} = 0;\n");
    $code->writePhp("}\n");
    
    $code->writePhp("else {\n");
      $code->writePhp("if ({$this->elipses_count_var} == 0) {\n");
        $elipses_tag->generateNow($code);
      $code->writePhp("}\n");
      $code->writePhp("{$this->elipses_count_var} += 1;\n");
      $code->writePhp("{$this->show_separator_var} = false;\n");
    $code->writePhp("}\n");

    $code->writePhp("{$this->pager}->nextPage();\n");
  }

  protected function _generateNumber($code)
  {
    $code->writePhp("if (!({$this->pager}->isFirst() && {$this->pager}->isLast())) {\n");

    if ($number_child = $this->findChildByClass('lmbMacroPagerNumberTag'))
      $number_child->generate($code);

    if($current_child = $this->findChildByClass('lmbMacroPagerCurrentTag'))
      $current_child->generate($code);

    $code->writePhp("}\n");
  }

  protected function _generateSeparator($code)
  {
    if ($separator_tag = $this->findChildByClass('lmbMacroPagerSeparatorTag'))
    {
      $code->writePhp("if ({$this->pager}->isValid()){\n");
      $separator_tag->generateNow($code);
      $code->writePhp("}\n");
    }
  }

  protected function _generateSection($code)
  {
    $section_child = $this->findChildByClass('lmbMacroPagerSectionTag');
    if($section_child)
      $section_child->generate($code);
  }
}


