<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    wact
 */
/**
* @tag perform
* @req_const_attributes command
*/
class WactPerformTag extends WactRuntimeComponentTag
{
  protected $runtimeComponentName = 'WactPerformComponent';
  protected $runtimeIncludeFile = 'limb/wact/src/components/perform/WactPerformComponent.class.php';

  function generateContents($code)
  {
    parent :: generateContents($code);

    $code->writePhp($this->getComponentRefCode() . '->setCommand("' . $this->getAttribute('command') .'");' . "\n");

    if($this->hasAttribute('include'))
    $code->writePhp($this->getComponentRefCode() . '->setIncludePath("' . $this->getAttribute('include') .'");' . "\n");

    if($this->hasAttribute('method'))
      $code->writePhp($this->getComponentRefCode() . '->setMethod("' . $this->getAttribute('method') .'");' . "\n");

    $code->writePhp(' echo ' . $this->getComponentRefCode() . '->process($template);' . "\n");
  }

}

?>