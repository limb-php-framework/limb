<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2008 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/core/src/lmbDelegate.class.php');

/**
 * Delegates chain
 * 
 * @package core
 * @version $Id: lmbDelegatesChain.class.php 6942 2008-04-17 17:38:14Z svk $
 */
class lmbDelegatesChain 
{
  
  /**
   * Array of delegates
   *
   * @var array
   */
  protected $delegates = array();
  
  /**
   * Find a delegate added to chain
   *
   * @param mixed $delegate finding delegate 
   * @return mixed number of delegate or false if delegate is not found 
   */
  function find($delegate)
  {
    $delegate = lmbDelegate::objectify($delegate);
    foreach($this->delegates as $n => $dlg)
    {
      if($delegate->equal($dlg))
        return $n;
    }
    return false;
  }
  
  /**
   * Return true if delegate was added to the chain already 
   *
   * @param mixed $delegete
   * @return boolean
   */
  function exists($delegete)
  {
    return $this->find($delegete) !== false;
  }

  /**
   * Add a delegate to be invoked
   *
   * @param mixed $delegete
   */
  function add($delegete)
  {
    $this->delegates[] = lmbDelegate::objectify($delegete);
  }
  
  /**
   * Remove delegate from the chain
   *
   * @param mixed $delegete
   */
  function remove($delegete)
  {
    if(($num = $this->find($delegete)) !== false)
      unset($this->delegates[$num]);
  }
  
  /**
   * Invoke all delegates containing in the chain.
   * Stops invoking if delegate return a not null result
   *
   */
  function invoke()
  {
    $args = func_get_args();
    return lmbDelegate::invokeChain($this->delegates, $args); 
  }
  
}