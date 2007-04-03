<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: wysiwyg.tag.php 5510 2007-04-03 07:33:31Z pachanga $
 * @package    wysiwyg
 */
require_once('limb/wact/src/tags/form/control.inc.php');
define('LIMB_WYSIWYG_DIR', dirname(__FILE__) . '/../../../');

/**
* @tag wysiwyg
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

  function preGenerate($code)
  {
  }

  function generateContents($code)
  {
    $code->writePhp($this->getComponentRefCode() . '->initWysiwyg("'. $this->ini_file_name . '","'.$this->type.'" );');
    $code->writePhp($this->getComponentRefCode() . '->renderContents();');
  }

  function postGenerate($code)
  {
  }

}
?>