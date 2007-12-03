<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/macro/src/tags/form/lmbMacroRuntimeWidgetTag.class.php');

/**
 * Macro analog for html <form> tag
 * @tag form
 * @package macro
 * @version $Id$
 */
class lmbMacroFormTag extends lmbMacroRuntimeWidgetTag
{
  protected $html_tag = 'form';
  protected $widget_class_name = 'lmbMacroFormWidget';
  protected $widget_include_file = 'limb/macro/src/tags/form/lmbMacroFormWidget.class.php';
  
  protected function _generateBeforeOpeningTag($code)
  {
    $form = $this->getWidgetVar();
    
    // передача указанного контейнера с данными в виджет формы
    if($this->has('from'))
    {
      $from = $this->get('from');       
      $code->writePHP("{$form}->setDatasource({$from});\n");
      $this->remove('from');
    }
    
    $error_list_id = $form . '_error_list';
    // передача установленного в шаблон списка ошибок формы
    $code->writePHP("if(isset({$error_list_id})){$form}->setErrorList({$error_list_id});\n");
    
    parent :: _generateBeforeOpeningTag($code);
  }
}

