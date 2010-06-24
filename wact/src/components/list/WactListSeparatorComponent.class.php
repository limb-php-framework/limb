<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Represents list tags separator at runtime.
 * Created just to simplify list:separator tag code
 * @package wact
 * @version $Id$
 */
class WactListSeparatorComponent extends WactRuntimeComponent
{
  protected $step = 1;
  protected $step_counter = 0;
  protected $total = 0;
  protected $total_counter = 0;
  protected $list_component = null;
  protected $skip_next = false;

  function setStep($step)
  {
    if($step < 1)
      $step = 1;

    $this->step = $step;
  }

  function next()
  {
    if($this->skip_next)
      $this->step_counter = 0;
    else
      $this->step_counter++;

    $this->total_counter++;
  }

  function reset()
  {
    $this->step_counter = 0;
  }

  function prepare()
  {
    $this->step_counter = 0;
    $this->total_counter = 0;

    if(!$this->list_component)
    {
      $this->list_component = $this->findParentByClass('WactListComponent');
      if(!$this->list_component)
        throw new WactException('Parent List Component not found');
    }

    $this->total = $this->list_component->countPaginated();
  }

  function shouldDisplay()
  {
    if($this->skip_next ||
       ($this->step_counter != $this->step) ||
       ($this->total_counter >= $this->total) ||
       !$this->list_component->valid())
    {
      $result = false;
    }
    else
      $result = true;

    $this->skip_next = false;

    return $result;
  }

  function skipNext()
  {
    $this->reset();
    $this->skip_next = true;
  }
}

