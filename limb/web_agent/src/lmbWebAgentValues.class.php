<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

lmb_require('limb/core/src/lmbObject.class.php');

/**
 * Web agent values
 *
 * @package web_agent
 * @version $Id: lmbWebAgentValues.class.php 89 2007-10-12 15:28:50Z CatMan $
 */
class lmbWebAgentValues extends lmbObject {

  function buildQuery($encoding = 'utf-8')
  {
  	return http_build_query($this->convert($encoding));
  }

  function convert($encoding)
  {
    $vars = $this->export();
    if($encoding != 'utf-8')
    {
      foreach($vars as &$var)
        $var = mb_convert_encoding($var, $encoding, 'utf-8');
    }
    return $vars;
  }

}
