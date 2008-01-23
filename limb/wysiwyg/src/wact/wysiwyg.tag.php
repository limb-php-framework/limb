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
 * @version $Id: wysiwyg.tag.php 6735 2008-01-23 14:07:39Z serega $
 */
class lmbWysiwygTag extends WactControlTag
{
  var $runtimeComponentName = 'lmbWysiwygComponent';
  var $runtimeIncludeFile = 'limb/wysiwyg/src/wact/lmbWysiwygComponent.class.php';
  var $profile;

  function prepare()
  {
    $this->determineComponent();
  }

  function determineComponent()
  {
    try
    {
      $ini = lmbToolkit :: instance()->getConf('wact_wisywyg.ini');
    }
    catch(lmbException $e){}
    
    if(!$ini)
      $ini = lmbToolkit :: instance()->getConf('wisywyg.ini');

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

