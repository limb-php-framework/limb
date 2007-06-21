<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
require_once('limb/wact/src/tags/form/control.inc.php');
define('LIMB_WYSIWYG_DIR', dirname(__FILE__) . '/../../../');

/**
 * @tag richedit,wysiwyg
 * @package wysiwyg
 * @version $Id: wysiwyg.tag.php 6009 2007-06-21 09:19:18Z serega $
 */
class lmbWysiwygTag extends WactControlTag
{
  var $runtimeComponentName = 'lmbWysiwygComponent';
  var $runtimeIncludeFile = 'limb/wysiwyg/src/template/components/lmbWysiwygComponent.class.php';
  var $ini_file_name = 'wysiwyg.ini';
  var $profile;

  function prepare()
  {
    $this->determineComponent();
  }

  function determineComponent()
  {
    $ini = lmbToolkit :: instance()->getConf($this->ini_file_name);

    if(($this->profile = $this->getAttribute('profile')) == '' &&
       ($this->profile = $ini->getOption('profile')) == '')
    {
       $this->profile = null;
       return;
    }

    if($ini->getOption('runtimeIncludeFile', $this->profile))
      $this->runtimeIncludeFile = $ini->getOption('runtimeIncludeFile', $this->profile);
    if($ini->getOption('runtimeComponentName', $this->profile))
      $this->runtimeComponentName = $ini->getOption('runtimeComponentName', $this->profile);

  }

  protected function _renderOpenTag($code_writer)
  {
  }

  protected function _renderCloseTag($code_writer)
  {
  }

  function generateTagContent($code)
  {
    if(isset($this->attributeNodes['name']) && !$this->attributeNodes['name']->isConstant())
    {
      $code->writePhp($this->getComponentRefCode() . '->setAttribute("name", ');
      $code->writePhp($this->attributeNodes['name']->generateExpression($code));
      $code->writePhp(');' . "\n");
    }
    $code->writePhp($this->getComponentRefCode() . '->initWysiwyg("'. $this->ini_file_name . '","'.$this->profile.'" );' . "\n");
    $code->writePhp($this->getComponentRefCode() . '->renderContents();' . "\n");
  }
}
?>