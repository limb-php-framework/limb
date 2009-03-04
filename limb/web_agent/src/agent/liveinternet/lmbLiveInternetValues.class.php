<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require(dirname(__FILE__).'/../../lmbWebAgentValues.class.php');

/**
 * Values of Liveinternet agent
 *
 * @package web_agent
 * @version $Id: lmbLiveInternetValues.class.php 89 2007-10-12 15:28:50Z CatMan $
 */
class lmbLiveInternetValues extends lmbWebAgentValues {

  function buildQuery($encoding = 'utf-8')
  {
  	$vars = array();
    foreach($this->convert($encoding) as $name => $value)
    {
    	if(is_array($value))
      {
      	foreach($value as $v)
        {
        	$vars[] = http_build_query(array($name => $v), '', ';');
        }
      }
      else
        $vars[] = http_build_query(array($name => $value), '', ';');
    }
    return implode(';', $vars);
  }

}
