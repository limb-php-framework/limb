<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package swishe
 * @version $Id$
 */

class lmbSwishFocus
{
  protected $radius;
  protected $ellipses;

  function __construct($radius, $ellipses = '...')
  {
    $this->radius = $radius;
    $this->ellipses = $ellipses;
  }

  function process($query, $result)
  {
    $items = $this->_parseQuery($query);

    $split_regex = '\b(' . implode('|', array_map('preg_quote', $items)) . ')';
    $split_regex = "~$split_regex~i";

    $splitted = preg_split($split_regex, $result, -1, PREG_SPLIT_DELIM_CAPTURE);

    $focused = '';
    for($i=1;$i<count($splitted);$i=$i+2)
    {
      if($i == 1)
        $left = $this->_gapLeft($splitted[$i-1]);
      else
        $left = $this->_gap($splitted[$i-1]);

      $match = $splitted[$i];
      $focused .= ' ' . $left . ' ' . $match;
    }

    if(isset($splitted[$i-1]))
      $focused .= ' ' . $this->_gapRight($splitted[$i-1]);

    return trim($focused);
  }

  protected function _gapRight($content)
  {
    $words = explode(' ', trim($content));
    $total = sizeof($words);
    if($total > $this->radius)
    {
      $tmp = array();

      for($i=0;$i<$this->radius;$i++)
        $tmp[] = $words[$i];

      $tmp[] = $this->ellipses;
      return implode(' ', $tmp);
    }
    else
      return implode(' ', $words);
  }

  protected function _gapLeft($content)
  {
    $words = explode(' ', trim($content));
    $total = sizeof($words);
    if($total > $this->radius)
    {
      $tmp = array();

      $tmp[] = $this->ellipses;

      for($i=$total-$this->radius;$i<$total;$i++)
        $tmp[] = $words[$i];

      return implode(' ', $tmp);
    }
    else
      return implode(' ', $words);
  }

  protected function _gap($content)
  {
    $words = explode(' ', trim($content));
    $total = sizeof($words);
    if($total >= $this->radius * 2)
    {
      $tmp = array();

      for($i=0;$i<$this->radius;$i++)
        $tmp[] = $words[$i];

      $tmp[] = $this->ellipses;

      for($i=$total-$this->radius;$i<$total;$i++)
        $tmp[] = $words[$i];

      return implode(' ', $tmp);
    }
    else
      return implode(' ', $words);
  }

  protected function _parseQuery($query)
  {
    $query = strtolower($query);
    $query = preg_replace('~\s+~', ' ', $query);
    $query = str_replace('(', '', $query);
    $query = str_replace(')', '', $query);
    $query = str_replace('"', '', $query);
    $query = str_replace("'", '', $query);
    $query = str_replace('*', '', $query);
    $query = str_replace(' and ', ' ', $query);
    $query = str_replace(' or ', ' ', $query);
    $query = str_replace(' not ', ' ', $query);
    $query = str_replace('!', '', $query);
    $query = str_replace('&', '', $query);
    $query = str_replace('|', '', $query);
    $query = trim($query);
    $items = array_unique(explode(' ', $query));
    arsort($items);
    return $items;
  }
}

