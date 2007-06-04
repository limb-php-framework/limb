<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCompositeRequestDispatcher.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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