<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: route_url.tag.php 5820 2007-05-07 10:34:13Z dan82 $
 * @package    web_app
 */
/**
* @tag route_url
* @suppress_attributes params route extra skip_controller
*/
class lmbRouteURLTag extends WactRuntimeComponentHTMLTag
{
  //protected $runtimeComponentName = 'WactRuntimeTagComponent';

  function getRenderedTag()
  {
    return 'a';
  }

  function preGenerate($code)
  {
    $href = '$' . $code->getTempVariable();

    $route = '$' . $code->getTempVariable();
    $code->writePhp($route. ' = "";');
    if(isset($this->attributeNodes['route']))
      $code->writePhp($route. ' = "'. $this->attributeNodes['route']->getValue() . '";');

    $params = '$' . $code->getTempVariable();
    $code->writePhp($params . ' = array();');

    
    
    if(isset($this->attributeNodes['params']))
    {
      $this->attributeNodes['params']->generatePreStatement($code);
      $code->writePhp($params . ' = lmbArrayHelper :: explode(",", ":",');
      $this->attributeNodes['params']->generateExpression($code);
      $code->writePhp(');');
      $this->attributeNodes['params']->generatePostStatement($code);
    }

    if($this->getBoolAttribute('skip_controller')) $skip_controller = 'true';
    else $skip_controller = 'false';
    
    $this->removeAttribute('skip_controller');
    $code->writePhp($href . '= lmbToolkit :: instance()->getRoutesUrl(' . $params . ', ' . $route .', ' . $skip_controller .');');
   
    $this->removeAttribute('route');
    $this->removeAttribute('params');

    if(isset($this->attributeNodes['extra']))
    {
      $params = '$' . $code->getTempVariable();
      $code->writePhp($params . ' = array();');

      $this->attributeNodes['extra']->generatePreStatement($code);
      $code->writePhp($href . ' .= ');
      $this->attributeNodes['extra']->generateExpression($code);
      $code->writePhp(';');
      $this->attributeNodes['extra']->generatePostStatement($code);

      $this->removeAttribute('extra');
    }

    $code->writePhp($this->getComponentRefCode() .
                    '->setAttribute("href", ' . $href . ');');

    parent :: preGenerate($code);
  }
}

?>