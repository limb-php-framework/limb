<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
/**
 * @tag route_url_set
 * @forbid_end_tag
 * @req_const_attributes field
 * @package web_app
 * @version $Id: route_url_set.tag.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbRouteUrlSetTag extends WactCompilerTag
{
  function generateTagContent($code)
  {
    $route = '$' . $code->getTempVariable();
    $code->writePhp($route. ' = "";');
    if(isset($this->attributeNodes['route']))
      $code->writePhp($route. ' = "'. $this->attributeNodes['route']->getValue() . '";');

    $fake_params = '$' . $code->getTempVariable();
    $params = '$' . $code->getTempVariable();
    $code->writePhp($fake_params . ' = array();');
    $code->writePhp($params . ' = array();');

    if(isset($this->attributeNodes['params']))
    {
      $code->writePhp($fake_params . ' = lmbArrayHelper :: explode(",",":",');
      $this->attributeNodes['params']->generateExpression($code);
      $code->writePhp(');');
      $code->writePhp('foreach(' . $fake_params . ' as $key => $value) ' . $params . '[trim($key)] = trim($value);');
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


