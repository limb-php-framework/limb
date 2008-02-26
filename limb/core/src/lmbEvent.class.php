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
 * Event object. In fact it's a collection of delegates
 * 
 * @package core
 * @version $Id: lmbEvent.class.php 6806 2008-02-26 15:58:38Z cmz $
 */
class lmbEvent 
{
  
  /**
   * Array of delegates
   *
   * @var array
   */
  protected $delegates = array();
  
  /**
   * Find a delegate added to event
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
   * Return true if delegate was added to the event already 
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
   * Remove delegate from the event object
   *
   * @param mixed $delegete
   */
  function remove($delegete)
  {
    if(($num = $this->find($delegete)) !== false)
      unset($this->delegates[$num]);
  }
  
  /**
   * Invoke all delegates containing in the event object.
   *
   */
  function invokeAll()
  {
    $args = func_get_args();
    lmbDelegate::invokeAll($this->delegates, $args);
  }
  
  /**
   * Invoke all delegates containing in the event object.
   * Stops invoking if delegate return a not null result
   *
   */
  function invokeChain()
  {
    $args = func_get_args();
    return lmbDelegate::invokeChain($this->delegates, $args); 
  }
  
}
?>