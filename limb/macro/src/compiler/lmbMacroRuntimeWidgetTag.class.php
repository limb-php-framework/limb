<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @package macro
 * @version $Id$
 */
class lmbMacroRuntimeWidgetTag extends lmbMacroTag
{
  protected $widget_include_file;
  protected $widget_class_name;
  protected $html_tag;
  
  function preParse()
  {
    if(!$this->widget_class_name)
      $this->raise('Please specify "$widget_class_name" property of the tag class "' . get_class($this) .'"');

    if(!$this->html_tag)
      $this->raise('Please specify "$html_tag" property of the tag class "' . get_class($this) .'"');
  }

  protected function _generateBeforeContent($code_writer)
  {
    $this->_generateWidget($code_writer);
    
    $this->_generateBeforeOpeningTag($code_writer);
    $this->_generateOpeningTag($code_writer);
    $this->_generateAfterOpeningTag($code_writer);
  }
    
  protected function _generateAfterContent($code_writer)
  {
    $this->_generateBeforeClosingTag($code_writer);
    $this->_generateClosingTag($code_writer);
    $this->_generateAfterClosingTag($code_writer);
  }
  
  function getRuntimeVar()
  {
    return '$this->' . $this->html_tag . '_' . $this->getRuntimeId();
  }
  
  function getRuntimeId()
  {
    if ($this->hasConstant('runtime_id'))
      return $this->get('runtime_id');
    elseif($this->hasConstant('id'))
      return $this->get('id');
    elseif($this->hasConstant('name'))
      return $this->get('name');
    else
    {
      $runtime_id = self :: generateNewRuntimeId();
      $this->set('runtime_id', $runtime_id);
      return $runtime_id;
    }
  }   

  static function generateNewRuntimeId()
  {
    static $counter = 1;
    return 'id00' . $counter++;
  }

  function _generateWidget($code_writer)
  {
    if ($this->widget_include_file)
      $code_writer->registerInclude($this->widget_include_file);

    $runtime_id = $this->getRuntimeId();
    $widget = $this->getRuntimeVar();
    $code_writer->writeToInit("{$widget} = new {$this->widget_class_name}('{$runtime_id}');\n");
    $code_writer->writeToInit("{$widget}->setAttributes(" . var_export($this->getConstantAttributes(), true) . ");\n");
  }

  protected function _generateOpeningTag($code)
  {
    $this->_generateDynamicAttributes($code);

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

