<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

function wact_stats_filter($value, $id, $mode='acc')
{
  static $stat_finder_singleton = array();

  if (!array_key_exists(0,$stat_finder_singleton))
      $stat_finder_singleton[0] = new WactStatFilterFinder();

  $stat_finder = $stat_finder_singleton[0];
  $stat = $stat_finder->getById($id);

  return $stat->perform($mode, $value);
}

/**
 * class WactStatFilterFinder.
 *
 * @package wact
 * @version $Id: stats_filter.inc.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactStatFilterFinder
{
    var $stat_filters;

    function __construct()
    {
      $this->_initStatic();
    }

    function _initStatic()
    {
      static $stats;
      if (!is_array($stats))
        $stats = array();
      $this->stat_filters = $stats;
    }

    function getById($id)
    {
      if (!array_key_exists($id, $this->stat_filters))
        $this->stat_filters[$id] = new WactStatFilter();
      return $this->stat_filters[$id];
    }
}

/**
 * class WactStatFilter.
 *
 * @package wact
 * @version $Id: stats_filter.inc.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactStatFilter
{
  var $hist = array();
  var $sum = 0;
  var $count = 0;

  function perform($mode, $value)
  {
    switch (strtolower($mode))
    {
      case 'acc':
          $this->hist[] = (float)$value;
          $this->sum += (float)$value;
          $this->count++;
          return $value;
          break;
      case 'accq':
          $this->hist[] = (float)$value;
          $this->sum += (float)$value;
          $this->count++;
          break;
      case 'sum':
          return $this->sum;
          break;
      case 'avg':
          return ($this->count) ? $this->sum / $this->count : 0;
          break;
      case 'count':
          return $this->count;
          break;
      case 'stdev':
          $avg = (float)(($this->count) ? $this->sum / $this->count : 0);
          $sumdev = 0;
          foreach ($this->hist as $val) {
              $sumdev += pow(($val - $avg),2);
          }
          return ($this->count-1) ? sqrt($sumdev/($this->count-1)) : 0;
          break;
      case 'stdevp':
          $avg = (float)(($this->count) ? $this->sum / $this->count : 0);
          $sumdev = 0;
          foreach ($this->hist as $val) {
              $sumdev += pow(($val - $avg),2);
          }
          return ($this->count) ? sqrt($sumdev/($this->count)) : 0;
          break;
      case 'reset':
          $this->hist = array();
          $this->sum = 0;
          $this->count = 0;
          break;
      default:
          die("implement a WACT error for undefined mode '$mode'");
    }
  }
}


