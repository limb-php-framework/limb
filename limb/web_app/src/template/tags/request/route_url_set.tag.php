<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
/**
* @tag route_url_set
* @forbid_end_tag
* @req_const_attributes field
*/
class lmbRouteUrlSetTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $route = '$' . $code->getTempVariable();
    $code->writePhp($route. ' = "";');
    if(isset($this->attributeNodes['route']))
      $code->writePhp($route. ' = "'. $this->attributeNodes['route']->getValue() . '";');

    $params = '$' . $code->getTempVariable();
    $code->writePhp($params . ' = array();');

    if(isset($this->attributeNodes['params']))
    {
      $code->writePhp($params . ' = lmbArrayHelper :: explode(",",":",');
      $this->attributeNodes['params']->generateExpression($code);
      $code->writePhp(');');
    }

    $skip_controller = '$' . $code->getTempVariable();

    if($this->getBoolAttribute('skip_controller'))
      $code->writePhp($skip_controller . ' = true;');
    else
      $code->writePhp($skip_controller . ' = false;');


    $code->writePhp($this->parent->getDatasource()->getComponentRefCode() .
                    '->set("' . $this->getAttribute('field') . '",
                           lmbToolkit :: instance()->getRoutesUrl(' . $params . ', ' . $route . ', ' . $skip_controller .'));');
  }
}

?>