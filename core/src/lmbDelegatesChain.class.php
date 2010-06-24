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
 * @version $Id: lmbDelegatesChain.class.php 7996 2009-09-30 13:52:25Z cmz $
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
   * @param mixed $delegate
   * @return boolean
   */
  function exists($delegate)
  {
    return $this->find($delegate) !== false;
  }

  /**
   * Add a delegate to be invoked
   *
   * @param mixed $delegate
   */
  function add($delegate)
  {
    $this->delegates[] = lmbDelegate::objectify($delegate);
  }
  
  /**
   * Remove delegate from the chain
   *
   * @param mixed $delegate
   */
  function remove($delegate)
  {
    if(($num = $this->find($delegate)) !== false)
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