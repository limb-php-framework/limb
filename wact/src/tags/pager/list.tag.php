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
 * @parent_tag_class WactPagerNavigatorTag
 * @package wact
 * @version $Id: list.tag.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactPagerListTag extends WactCompilerTag
{
  protected $navigator;
  protected $navigator_component;
  protected $sections_mode = true;
  protected $show_separator_var = '';
  protected $elipses_count_var = '';

  function generateBeforeContent($code)
  {
    $this->navigator = $this->findParentByClass('WactPagerNavigatorTag');
    $this->navigator_component = $this->navigator->getComponentRefCode();

    if($this->findChildByClass('WactPagerElipsesTag'))
      $this->sections_mode = false;

    $this->show_separator_var = $code->getTempVarRef();
    $code->writePhp($this->show_separator_var . ' = false;' . "\n");

    $this->elipses_count_var = $code->getTempVarRef();
    $code->writePhp($this->elipses_count_var . ' = 0;' . "\n");

    $parent = $this->findParentByClass('WactPagerNavigatorTag');
    $code->writePhp('while (' . $parent->getComponentRefCode() . '->isValid()) {');
  }

  function generateTagContent($code)
  {
    if($this->sections_mode)
      $this->_generateForSectionsMode($code);
    else
      $this->_generateForElipsesMode($code);
  }

  function generateAfterContent($code)
  {
    $code->writePhp('}');
  }

  protected function _generateForSectionsMode($code)
  {
    $code->writePhp('if (' . $this->navigator_component . '->isDisplayedSection())');
    $code->writePhp('{');
      $this->_generateNumber($code);
      $code->writePhp($this->navigator_component . '->nextPage();' . "\n");
      $this->_generateSeparator($code);
    $code->writePhp('}');
    $code->writePhp('else');
    $code->writePhp('{');
      $this->_generateSection($code);
      $code->writePhp($this->navigator_component . '->nextSection();' . "\n");
    $code->writePhp('}');
  }

  protected function _generateForElipsesMode($code)
  {
    $sep_child = $this->findChildByClass('WactPagerSeparatorTag');
    $elipses_child = $this->findChildByClass('WactPagerElipsesTag');

    if ($sep_child)
    {
      $code->writePhp('if (');
      $code->writePhp($this->show_separator_var);
      $code->writePhp('&& ');
      $code->writePhp($this->navigator_component . '->shouldDisplayPage()');
      $code->writePhp(')');
      $code->writePhp('{');
        $sep_child->generateNow($code);
      $code->writePhp('}');
      $code->writePhp($this->show_separator_var . ' = TRUE;' . "\n");
    }

    $code->writePhp('if (' . $this->navigator_component . '->shouldDisplayPage())');
    $code->writePhp('{');
      $this->_generateNumber($code);
      $code->writePhp($this->elipses_count_var . ' = 0;' . "\n");
    $code->writePhp('}' . "\n");
    $code->writePhp('else');
    $code->writePhp('{');
      $code->writePhp('if (' . $this->elipses_count_var . ' == 0) {');
        $elipses_child->generateNow($code);
      $code->writePhp('}' . "\n");
      $code->writePhp($this->elipses_count_var . ' += 1;' . "\n");
      $code->writePhp($this->show_separator_var . ' = FALSE;' . "\n");
    $code->writePhp('}' . "\n");

    $code->writePhp($this->navigator_component . '->nextPage();' . "\n");
  }

  protected function _generateNumber($code)
  {
    $number_child = $this->findChildByClass('WactPagerNumberTag');
    $current_child = $this->findChildByClass('WactPagerDisplayedTag');

    $code->writePhp('if (!(' . $this->navigator_component . '->isFirst() && ' .
                               $this->navigator_component . '->isLast())) {');

    if ($number_child)
      $number_child->generate($code);

    if($current_child)
      $current_child->generate($code);

    $code->writePhp('}' . "\n");
  }

  protected function _generateSeparator($code)
  {
    $sep_child = $this->findChildByClass('WactPagerSeparatorTag');

    if ($sep_child)
    {
      $code->writePhp('if (' . $this->navigator_component . '->isValid())' . "\n");
      $code->writePhp('{' . "\n");
      $sep_child->generateNow($code);
      $code->writePhp('}' . "\n");
    }
  }

  protected function _generateSection($code)
  {
    $section_child = $this->findChildByClass('WactPagerSectionTag');
    if($section_child)
      $section_child->generate($code);
  }
}


