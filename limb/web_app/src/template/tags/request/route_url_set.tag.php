<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: route_url_set.tag.php 5787 2007-05-02 13:46:30Z tony $
 * @package    web_app
 */
/**
* @tag route_url_set
* @forbid_end_tag
* @req_const_attributes field
*/
class lmbRouteUrlSetTag extends WactCompilerTag
{
  function generateContents($code)
  {
    $route = '$' . $code->getTempVariable();
    $code->writePhp($route. ' = "";');
    if(isset($this->attributeNodes['route']))
      $code->writePhp($route. ' = "'. $this->attributeNodes['route']->getValue() . '";');

    $params = '$' . $code->getTempVariable();
    $code->writePhp($params . ' = array();');

    if(isset($this->attributeNodes['params']))
    {
      $this->attributeNodes['params']->generatePreStatement($code);
      $code->writePhp($params . ' = lmbArrayHelper :: explode(",",":",');
      $this->attributeNodes['params']->generateExpression($code);
      $code->writePhp(');');
      $this->attributeNodes['params']->generatePostStatement($code);
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