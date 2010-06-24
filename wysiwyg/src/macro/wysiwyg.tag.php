<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright © 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/tags/form/textarea.tag.php');
lmb_require('limb/wysiwyg/src/lmbWysiwygConfigurationHelper.class.php');

/**
 * Macro wysiwyg tag
 * @tag wysiwyg
 * @package wysiwyg
 * @version $Id$
 */
class lmbMacroWysiwygTag extends lmbMacroTextAreaTag
{
  /**
   * @var lmbWysiwygConfigurationHelper
   */
  protected $_helper;
  
  function preParse($compiler)
  {
    parent :: preParse($compiler);

    // always has closing tag
    $this->has_closing_tag = true;
    
    $this->_helper = new lmbWysiwygConfigurationHelper();
    if($profile_name = $this->get('profile'))
      $this->_helper->setProfileName($profile_name);
    
    $this->_determineWidget();
  }  

  protected function _determineWidget()
  {  
    $component_info = $this->_helper->getMacroWidgetInfo();    
    $this->widget_include_file = $component_info['file'];
    $this->widget_class_name = $component_info['class'];
      
    $this->html_tag = 'wysiwyg';
    $this->has_closing_tag = false;
    $this->set('profile_name', $this->_helper->getProfileName());    
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
