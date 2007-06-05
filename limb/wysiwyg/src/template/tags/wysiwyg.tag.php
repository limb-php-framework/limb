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

  function generateBeforeContent($code)
  {
  }

  function generateTagContent($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->initWysiwyg("'. $this->ini_file_name . '","'.$this->profile.'" );');
    $code->writePhp($this->getComponentRefCode() . '->renderContents();');
  }

  function generateAfterContent($code)
  {
  }

}
?>