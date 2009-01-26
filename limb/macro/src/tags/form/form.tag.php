<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/compiler/lmbMacroRuntimeWidgetHtmlTag.class.php');

/**
 * Macro analog for html <form> tag
 * @tag form
 * @package macro
 * @version $Id$
 */
class lmbMacroFormTag extends lmbMacroRuntimeWidgetHtmlTag
{
  protected $html_tag = 'form';
  protected $widget_class_name = 'lmbMacroFormWidget';
  protected $widget_include_file = 'limb/macro/src/tags/form/lmbMacroFormWidget.class.php';
  
  protected function _generateBeforeOpeningTag($code)
  {
    $form = $this->getRuntimeVar();
    
    // passing specified variable as a datasource to form widget
    if($this->has('from'))
    {
      $from = $this->get('from');       
      $code->writePHP("{$form}->setDatasource({$from});\n");
      $this->remove('from');
    }

    // passing specially named variable of the compiled template as a datasource to form widget if the variable if defined
    $datasource_id = $form . '_datasource';
    $code->writePHP("if(isset({$datasource_id})){$form}->setDatasource({$datasource_id});\n");
    
    // passing specially named variable of the compiled template as an error_list to form widget if the variable if defined
    $error_list_id = $form . '_error_list';
    $code->writePHP("if(isset({$error_list_id})){$form}->setErrorList({$error_list_id});\n");
    
    parent :: _generateBeforeOpeningTag($code);
  }
}

