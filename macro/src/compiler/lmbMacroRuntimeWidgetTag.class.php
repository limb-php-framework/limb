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
class lmbMacroRuntimeWidgetTag extends lmbMacroTag
{
  protected $widget_include_file;
  protected $widget_class_name;
  protected $runtime_var;
  
  function preParse($compiler)
  {
    if(!$this->widget_class_name)
      $this->raise('Please specify "$widget_class_name" property of the tag class "' . get_class($this) .'"');
  }

  protected function _generateBeforeContent($code_writer)
  {
    $this->_generateWidget($code_writer);
  }
    
  function getRuntimeVar()
  {
    if($this->runtime_var)
      return $this->runtime_var;
    $this->runtime_var = '$this->' . $this->tag . '_' . $this->getRuntimeId();
    return $this->runtime_var;
  }
  
  function getRuntimeId()
  {
    if ($this->hasConstant('runtime_id'))
      return $this->get('runtime_id');
    elseif($this->hasConstant('id'))
      return $this->get('id');
    elseif($this->hasConstant('name') && (strpos($this->get('name'), '[]') === false))
    {
      return $this->get('name');
    }
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

  protected function _generateWidget($code_writer)
  {
    if ($this->widget_include_file)
      $code_writer->registerInclude($this->widget_include_file);

    $runtime_id = $this->getRuntimeId();
    $widget = $this->getRuntimeVar();
    $code_writer->writeToInit("{$widget} = new {$this->widget_class_name}('{$runtime_id}');\n");
  }
}

