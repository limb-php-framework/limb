<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @package macro
 * @version $Id$
 */
class lmbMacroRuntimeWidgetHtmlTag extends lmbMacroRuntimeWidgetTag
{
  protected $html_tag;
  
  function preParse($compiler)
  {
    parent :: preParse($compiler);

    if(!$this->html_tag)
      $this->raise('Please specify "$html_tag" property of the tag class "' . get_class($this) .'"');
  }

  protected function _generateBeforeContent($code_writer)
  {
    parent :: _generateBeforeContent($code_writer);

    $this->_generateBeforeOpeningTag($code_writer);

    $this->_generateDynamicAttributes($code_writer);
    
    $this->_generateOpeningTag($code_writer);
    $this->_generateAfterOpeningTag($code_writer);
  }
    
  protected function _generateAfterContent($code_writer)
  {
    $this->_generateBeforeClosingTag($code_writer);
    $this->_generateClosingTag($code_writer);
    $this->_generateAfterClosingTag($code_writer);
  }
  
  protected function _generateWidget($code_writer)
  {
    parent :: _generateWidget($code_writer);
    
    $widget = $this->getRuntimeVar();
    $code_writer->writeToInit("{$widget}->setAttributes(" . var_export($this->getConstantAttributes(), true) . ");\n");
  }

  protected function _generateOpeningTag($code)
  {
    $code->writeHTML("<{$this->html_tag}");

    $code->writePHP($this->getRuntimeVar() . '->renderAttributes();');

    if (!$this->has_closing_tag)
      $code->writeHTML(' /');

    $code->writeHTML('>');
  }
  
  protected function _generateDynamicAttributes($code)
  {
    $widget = $this->getRuntimeVar();
    foreach(array_keys($this->attributes) as $key)
    {
      if ($this->attributes[$key]->isDynamic())
      {
        $value = $this->attributes[$key]->getValue();
        $code->writePHP("{$widget}->setAttribute('{$key}',");
        $code->writePHP($this->getEscaped($key));
        $code->writePHP(");\n");
      }
    } 
  }

  protected function _generateClosingTag($code)
  {
    if ($this->has_closing_tag)
      $code->writeHTML("</{$this->html_tag}>");
  }
  
  protected function _generateBeforeOpeningTag($code)
  {
  }

  protected function _generateAfterOpeningTag($code)
  {
  }
 
  protected function _generateBeforeClosingTag($code)
  {
  }

  protected function _generateAfterClosingTag($code)
  {
  }
}

