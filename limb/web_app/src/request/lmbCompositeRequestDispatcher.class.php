<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCompositeRequestDispatcher.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/request/lmbRequestDispatcher.interface.php');

class lmbCompositeRequestDispatcher implements lmbRequestDispatcher
{
  protected $dispatchers;

  function dispatch($request)
  {
    foreach($this->dispatchers as $dispatcher)
    {
      $result = $dispatcher->dispatch($request);
      if(isset($result['controller']))
        return $result;
    }

    return array();
  }

  function addDispatcher($dispatcher)
  {
    $this->dispatchers[] = $dispatcher;
  }
}

?>