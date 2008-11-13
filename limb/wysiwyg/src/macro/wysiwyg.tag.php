<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright © 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/tags/form/textarea.class.php');

/**
 * Macro wysiwyg tag
 * @tag wysiwyg
 * @package wysiwyg
 * @version $Id$
 */
class lmbMacroWysiwygTag extends lmbMacroTextAreaTag
{
  protected $ini_file_name = 'macro_wysiwyg.ini';
  protected $profile;

  function preParse($compiler)
  {
    parent :: preParse($compiler);

    // always has closing tag
    $this->has_closing_tag = true;
    
    $this->_determineWidgetType();
  }

  protected function _determineWidgetType()
  {
    $ini = lmbToolkit :: instance()->getConf($this->ini_file_name);
    if ($this->has('profile'))
    {
   	  $this->profile = $this->get('profile');
    }
    else
      $this->profile = $ini->getOption('profile');

    if($this->profile)
    {
       if($ini->getOption('widget_include_file', $this->profile))
          $this->widget_include_file = $ini->getOption('widget_include_file', $this->profile);
       if($ini->getOption('widget_class_name', $this->profile))
          $this->widget_class_name = $ini->getOption('widget_class_name', $this->profile);
        
       $this->html_tag = 'wysiwyg';
       $this->has_closing_tag = false;
       $this->set('ini_name',$this->ini_file_name);
       $this->set('profile', $this->profile);
     }
  }
  
  // rewriting parent behaviour since we don't need to render <wisywyg> tag 
  protected function _generateOpeningTag($code_writer)
  {
    $this->_generateWidget($code_writer);
  }

  protected function _generateClosingTag($code_writer)
  {
  }  

  protected function _generateContent($code)
  {
    $widget = $this->getRuntimeVar();
    $code->writePHP("{$widget}->renderWysiwyg();\n");
  }
}
