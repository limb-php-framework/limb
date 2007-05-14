<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: wysiwyg.tag.php 5888 2007-05-14 12:16:38Z alex433 $
 * @package    wysiwyg
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
  var $type = '';

  function prepare()
  {
    $this->determineComponent();
  }

  function determineComponent()
  {
    $ini = lmbToolkit :: instance()->getConf($this->ini_file_name);

    if(!$this->type = $ini->getOption('wysiwyg'))
    {
       $this->type = 'default';
       return;
    }

    if($ini->getOption('runtimeIncludeFile', $this->type))
      $this->runtimeIncludeFile = $ini->getOption('runtimeIncludeFile', $this->type);
    if($ini->getOption('runtimeComponentName', $this->type))
      $this->runtimeComponentName = $ini->getOption('runtimeComponentName', $this->type);

  }

  function generateBeforeContent($code)
  {
  }

  function generateTagContent($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->initWysiwyg("'. $this->ini_file_name . '","'.$this->type.'" );');
    $code->writePhp($this->getComponentRefCode() . '->renderContents();');
  }

  function generateAfterContent($code)
  {
  }

}
?>