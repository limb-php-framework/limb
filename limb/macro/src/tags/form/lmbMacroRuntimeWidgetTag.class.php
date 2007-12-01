<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/macro/src/lmbMacroTag.class.php');

/**
 * @package macro
 * @version $Id$
 */
class lmbMacroRuntimeWidgetTag extends lmbMacroTag
{
  protected $widget_include_file;
  protected $widget_class_name;
  protected $html_tag;
  
  function __construct($location, $tag, $tag_info, $html_tag)
  {
    parent :: __construct($location, $tag, $tag_info);

    $this->html_tag = $html_tag;
    
    if(!$this->widget_class_name)
      $this->raise('Please specify "$widget_class_name" property of the tag class "' . get_class($this) .'"');
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
  
  function getWidgetVar()
  {
    return '$this->' . $this->html_tag . '_' . $this->getWidgetId();
  }
  
  function getWidgetId()
  {
    if ($this->hasConstant('widget_id'))
      return $this->get('widget_id');
    elseif($this->hasConstant('id'))
      return $this->get('id');
    elseif($this->hasConstant('name'))
      return $this->get('name');
    else
    {
      $widget_id = self :: generateNewWidgetId();
      $this->set('widget_id', $widget_id);
      return $widget_id;
    }
  }   

  static function generateNewWidgetId()
  {
    static $counter = 1;
    return 'id00' . $counter++;
  }

  function _generateWidget($code_writer)
  {
    if ($this->widget_include_file)
      $code_writer->registerInclude($this->widget_include_file);

    $id = $this->getWidgetId();
    $widget = $this->getWidgetVar();
    $code_writer->writeToInit("{$widget} = new {$this->widget_class_name}('{$id}');\n");
    $code_writer->writeToInit("{$widget}->setAttributes(" . var_export($this->getConstantAttributes(), true) . ");\n");
  }

  protected function _generateOpeningTag($code)
  {
    $this->_generateDynamicAttributes($code);

    $code->writeHTML("<{$this->html_tag}");

    $code->writePHP($this->getWidgetVar() . '->renderAttributes();');

    if (!$this->has_closing_tag)
      $code->writeHTML(' /');

    $code->writeHTML('>');
  }
  
  protected function _generateDynamicAttributes($code)
  {
    $widget = $this->getWidgetVar();
    foreach(array_keys($this->attributes) as $key)
    {
      if ($this->attributes[$key]->isDynamic())
      {
        $value = $this->attributes[$key]->getValue();
        $code->writePHP("{$widget}->setAttribute('{$key}',");
        $code->writePHP($this->attributes[$key]->getEscaped());
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

