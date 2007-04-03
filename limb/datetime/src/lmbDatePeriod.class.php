<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbDatePeriod.class.php 4993 2007-02-08 15:35:44Z pachanga $
 * @package    datetime
 */
lmb_require('limb/datetime/src/lmbDate.class.php');
lmb_require('limb/core/src/exception/lmbException.class.php');

class lmbDatePeriod
{
  var $start;
  var $end;

  function __construct($start, $end)
  {
    $this->start = (is_object($start)) ? $start : new lmbDate($start);
    $this->end = (is_object($end)) ? $end : new lmbDate($end);

    if($this->end->isBefore($this->start))
      throw new lmbException('wrong period interval', array('start' => $this->start->toString(),
                                                             'end' => $this->end->toString()));
  }

  function toString()
  {
    return $this->start->toString() . ' - ' . $this->end->toString();
  }

  function getDuration()
  {
    return $this->end->getStamp() - $this->start->getStamp();
  }

  function getStart()
  {
    return $this->start;
  }

  function getEnd()
  {
    return $this->end;
  }

  function isEqual($period)
  {
    return $this->start->isEqual($period->getStart()) &&
           $this->end->isEqual($period->getEnd());
  }

  function includes($period)
  {
    return ($this->start->isBefore($period->getStart()) &&
            $this->end->isAfter($period->getEnd()));
  }

  function isInside($period)
  {
    return $period->includes($this);
  }

  function intersects($period)
  {
    return $this->isEqual($period)
           ||
           ($this->start->isBefore($period->getStart()) && $this->end->isAfter($period->getStart()))
           ||
           ($this->start->isAfter($period->getStart()) && $this->start->isBefore($period->getEnd()));
  }
}

?>
