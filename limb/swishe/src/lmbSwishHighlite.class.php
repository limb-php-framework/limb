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

class lmbSwishHighlite
{
  protected $marker_left;
  protected $marker_right;

  function __construct($marker_left, $marker_right)
  {
    $this->marker_left = $marker_left;
    $this->marker_right = $marker_right;
  }

  function process($query, $result)
  {
    $marked = $result;
    $items = $this->_parseQuery($query);
    foreach($items as $item)
    {
      $marked = preg_replace('~(?<!' . preg_quote($this->marker_left) . ')(' . preg_quote($item) . ')~i',
                             $this->marker_left . '$1' . $this->marker_right,
                             $marked);
    }
    return $marked;
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

