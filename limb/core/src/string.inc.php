<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * @package core
 * @version $Id$
 */
function lmb_camel_case($str, $ucfirst = true)
{
  //if there are no _, why process at all
  if(strpos($str, '_') === false)
    return $ucfirst ? ucfirst($str) : $str;

  $items = explode('_', $str);
  $len = sizeof($items);
  $first = true;
  $res = '';
  for($i = 0; $i < $len; $i++)
  {
    $item = $items[$i];
    if (!$item || is_numeric($item))
    {
      $res .= '_'. $item;
    }
    elseif (!$first)
    {
      $res .= ucfirst($item);
    }
    else
    {
      $res .= $ucfirst ? ucfirst($item) : $item;
      $first = false;
    }
  }

  return $res;
}

function lmb_under_scores($str)
{
  //caching repeated requests
  static $cache = array();
  if(isset($cache[$str]))
    return $cache[$str];

  $items = preg_split('~([A-Z][a-z0-9]+)~', $str, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
  $res = '';
  foreach($items as $item)
    $res .= ($item == '_' || $item[0] == '_' ? '' : '_') . strtolower($item);
  $res = substr($res, 1);
  $cache[$str] = $res;
  return $res;
}

function lmb_humanize($str)
{
  return str_replace('_', ' ', lmb_under_scores($str));
}

function lmb_plural($word)
{
  $plural_rules = array(
    '/person$/' => 'people', # person, salesperson
    '/man$/' => 'men', # man, woman, spokesman
    '/child$/' => 'children', # child
    '/(?:([^f])fe|([lr])f)$/' => '\1\2ves', # half, safe, wife
    '/([ti])um$/' => '\1a', # datum, medium
    '/(x|ch|ss|sh)$/' => '\1es', # search, switch, fix, box, process, address
    '/series$/' => '\1series',
    '/([^aeiouy]|qu)ies$/' => '\1y',
    '/([^aeiouy]|qu)y$/' => '\1ies', # query, ability, agency
    '/sis$/' => 'ses', # basis, diagnosis
    '/(.*)status$/' => '\1statuses',
    '/s$/' => 's', # no change (compatibility)
    '/$/' => 's'
  );

  $result = $word;
  foreach($plural_rules as $pattern => $repl)
  {
    $result = preg_replace ($pattern, $repl, $word);
    if ($result!= $word) break;
  }
  return $result;
}
