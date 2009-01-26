<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Web agent kit
 *
 * @package web_agent
 * @version $Id: lmbWebAgentKit.class.php 40 2007-10-04 15:52:39Z CatMan $
 */
class lmbWebAgentKit {

  function createRequest($req = 'socket')
  {
    $class = 'lmb'.ucfirst($req).'WebAgentRequest';

    $class_path = dirname(__FILE__).'/request/'.$class.'.class.php';
    lmb_require($class_path);

    return new $class;
  }

}
