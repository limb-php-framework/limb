<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
/**
 * @package web_app
 * @version $Id$
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');

class lmbProfileUtilsTools extends lmbAbstractTools
{

  protected $profile_points = array(
    '__start__' => 0,
    '__end__' => 0
  );

  protected $profile_diffs = array();

  function __construct()
  {
    $this->setProfileStartPoint();
  }

  function setProfileStartPoint($time = null)
  {
    $this->setProfilePoint('__start__', $time);
  }

  function setProfileEndPoint($time = null)
  {
    $this->setProfilePoint('__end__', $time);
  }

  function hasProfileStartPoint()
  {
    return !empty($this->profile_points['__start__']);
  }

  function hasProfileEndPoint()
  {
    return !empty($this->profile_points['__end__']);
  }

  function getProfilePoint($point)
  {
    if(!isset($this->profile_points[$point]))
    {
      throw new Exception("Point $point doesn't exist!");
    }
    return $this->profile_points[$point];
  }

  function getProfileDiffViews()
  {
    return $this->profile_diffs;
  }

  function setProfilePoint($point, $time = null)
  {
    $this->profile_points[$point] = $time ? $time : microtime(true);
  }

  function clearProfilePoint($point)
  {
    if($point == '__start__' || $point == '__end__')
    {
      throw new Exception("You can't unset start or end points!");
    }
    unset($this->profile_points[$point]);
  }

  protected function getProfilePreviousPoint($point)
  {
    if($point == '__start__')
    {
      return $this->getProfilePoint($point);
    }
    $profile_points = $this->profile_points;
    asort($profile_points);
    $keys = array_keys($profile_points);
    if(($key = array_search($point, $keys)) !== false)
    {
      return $this->getProfilePoint($keys[$key - 1]);
    }
    return $this->getProfilePoint($point);
  }

  function getProfileTimeDiff($first_point, $second_point = null)
  {
    // if no second point provided, assume it as previous
    if(is_null($second_point))
    {
      return abs($this->getProfilePoint($first_point) - $this->getProfilePreviousPoint($first_point));
    }
    return abs($this->getProfilePoint($first_point) - $this->getProfilePoint($second_point));
  }

  function getProfilePercentDiff($first_point, $second_point = null)
  {
    return 100 * $this->getProfileTimeDiff($first_point, $second_point) / $this->getProfileTotal();
  }

  function addProfileDiffView($first_point, $second_point, $custom_caption = null)
  {
    if($custom_caption)
    {
      $this->profile_diffs[$custom_caption] = array(
        'first_point' => $first_point,
        'second_point' => $second_point
      );
    }
    else
    {
      $this->profile_diffs[] = array(
        'first_point' => $first_point,
        'second_point' => $second_point
      );
    }
  }

  function getProfileTotal()
  {
    if(!$this->hasProfileEndPoint())
    {
      if(count($this->profile_points) > 2)
        $this->setProfileEndPoint(max($this->profile_points));
      else
        $this->setProfileEndPoint();
    }
    return $this->getProfileTimeDiff('__start__', '__end__');
  }

  function getProfileStatItem($first_point, $second_point = null)
  {
    $diff = $this->getProfileTimeDiff($first_point, $second_point);
    $percent = $this->getProfilePercentDiff($first_point, $second_point);
    return array(
      'time_diff' => $diff,
      'percent_diff' => $percent
    );
  }

  function showProfileStatItem($first_point, $second_point = null, $caption = null)
  {
    $data = $this->getProfileStatItem($first_point, $second_point);
    if(!$caption)
    {
      $caption = $second_point ? "$first_point <-> $second_point" : $first_point;
    }
    return sprintf("%s: %.7f sec. (%.2f%%)", $caption, $data['time_diff'], $data['percent_diff']);
  }

  function getProfileStat($echo_result = true)
  {
    $ret = "<pre>";
    foreach ($this->profile_points as $key => $item)
    {
      if($key == '__start__' || $key == '__end__')
      {
        continue;
      }
      $ret .= $this->showProfileStatItem($key) . "\n";
    }
    if($this->profile_diffs)
    {
      $ret .= "\nCustom profile points:\n";
    }
    foreach ($this->profile_diffs as $key => $item)
    {
      $ret .= $this->showProfileStatItem($item['first_point'], $item['second_point'], is_string($key) ? $key : null) . "\n";
    }
    $ret .= "\nTotal: {$this->getProfileTotal()} sec.";
    $ret .= "</pre>";
    
    if($echo_result)
    {
      echo $ret;
    }
    return $ret;
  }
}
