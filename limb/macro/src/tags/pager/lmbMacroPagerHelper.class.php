<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class lmbMacroPagerHelper.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroPagerHelper
{
  protected $id;
  
  protected $total_items = 0;
  protected $total_page_count = 0;
  protected $page_counter = 0;
  protected $current_page = 0;
  protected $items_per_page = 20;
  protected $base_url = null;

  protected $display_sections = true;
  protected $pages_per_section = 10;

  protected $display_elipses = false;
  protected $pages_in_middle = 5;
  protected $pages_in_sides = 3;
  
  function __construct($id)
  {
    $this->id = $id;

    $this->_initBaseUrl();
  }

  function prepare()
  {
    $this->total_page_count = ceil($this->total_items / $this->items_per_page);

    if ($this->total_page_count < 1)
      $this->total_page_count = 1;

    $this->_initCurrentPage();

    $this->page_counter = 1;
  }

  function useSections($flag = true)
  {
    $this->display_sections = $flag;
  }

  function isSectionsMode()
  {
    return $this->display_sections;
  }

  function useElipses($flag = true)
  {
    $this->useSections(!$flag);
    $this->display_elipses = $flag;
  }

  function isElipsesMode()
  {
    return $this->display_elipses;
  }

  function setPagesPerSection($pages)
  {
    $this->pages_per_section = $pages;
  }

  function setPagesInMiddle($pages)
  {
    $this->pages_in_middle = $pages;
  }

  function setPagesInSides($pages)
  {
    $this->pages_in_sides = $pages;
  }

  function setTotalItems($items)
  {
    $this->total_items = $items;
  }

  function getPagesPerSection()
  {
    return $this->pages_per_section;
  }

  function getTotalItems()
  {
    return $this->total_items;
  }

  function hasMoreThanOnePage()
  {
    return $this->total_items > $this->items_per_page;
  }

  function setItemsPerPage($items)
  {
    $this->items_per_page = $items;
  }
  
  function setCurrentPage($page)
  {
    $this->current_page = $page;
  }

  function getCurrentPageBeginItem()
  {
    if($this->total_items < 1)
      return 0;

    return $this->items_per_page * ($this->current_page - 1) + 1;
  }
  
  function getCurrentPageOffset()
  {
    $number = $this->getCurrentPageBeginItem();
    if(!$number)
      return 0;
    else
      return $number - 1;
  }

  function getCurrentPageEndItem()
  {
    $res = $this->items_per_page * $this->current_page;

    if($res > $this->total_items)
      return $this->total_items;
    else
      return $res;
  }

  function getItemsPerPage()
  {
    return $this->items_per_page;
  }

  function getTotalPages()
  {
    return $this->total_page_count;
  }

  function isFirst()
  {
    return ($this->current_page == 1);
  }

  function hasPrev()
  {
    return ($this->current_page > 1);
  }

  function hasNext()
  {
    return ($this->current_page < $this->total_page_count);
  }

  function isLast()
  {
    return ($this->current_page == $this->total_page_count);
  }

  protected function _initBaseUrl()
  {
    $this->base_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $pos = strpos($this->base_url, '?');
    if (is_integer($pos))
      $this->base_url = substr($this->base_url, 0, $pos);
  }
  
  protected function _initCurrentPage()
  {
    if(!$this->current_page && isset($_GET[$this->id]))
      $this->current_page = (int)$_GET[$this->id];

    if (empty($this->current_page))
      $this->current_page = 1;

    if($this->current_page > $this->total_page_count)
      $this->current_page = $this->total_page_count;
  }

  function nextPage()
  {
    $this->page_counter++;

    return $this->isValid();
  }

  function isValid()
  {
    return ($this->page_counter <= $this->total_page_count);
  }

  function nextSection()
  {
    $this->page_counter += $this->pages_per_section;

    return $this->isValid();
  }

  function getPage()
  {
    return $this->page_counter;
  }

  function isDisplayedPage()
  {
    return $this->page_counter == $this->current_page;
  }

  function shouldDisplayPage()
  {
    if($this->display_sections)
      return $this->isDisplayedSection();

    $half_windows_size = ($this->pages_in_middle - 1) / 2;
    return (
        $this->page_counter <= $this->pages_in_sides ||
        $this->page_counter > $this->total_page_count - $this->pages_in_sides ||
        ($this->page_counter >= $this->current_page - $half_windows_size &&
        $this->page_counter <= $this->current_page + $half_windows_size) ||
        ($this->page_counter == $this->pages_in_sides + 1 &&
        $this->page_counter == $this->current_page - $half_windows_size - 1) ||
        ($this->page_counter == $this->total_page_count - $this->pages_in_sides &&
        $this->page_counter == $this->current_page + $half_windows_size + 1));
  }

  function isDisplayedSection()
  {
    if($this->getSection() == $this->getDisplayedSection())
      return true;
    else
      return false;
  }

  function getSection()
  {
    return ceil($this->page_counter / $this->pages_per_section);
  }

  function getDisplayedSection()
  {
    return ceil($this->current_page / $this->pages_per_section);
  }

  function getSectionUri()
  {
    $section = $this->getSection();

    if ($section > $this->getDisplayedSection())
      return $this->getPageUri(($section - 1) * $this->pages_per_section + 1);
    else
      return $this->getPageUri($section  * $this->pages_per_section);
  }

  function getSectionBeginPage()
  {
    $result = ($this->getSection() - 1) * $this->pages_per_section + 1;

    if($result < 0)
      return 0;
    else
      return $result;
  }

  function getSectionEndPage()
  {
    $result = $this->getSection() * $this->pages_per_section;

    if ($result >= $this->total_page_count)
      $result = $this->total_page_count;

    return $result;
  }

  function getCurrentPageUri()
  {
    return $this->getPageUri($this->current_page);
  }

  function getCurrentPage()
  {
    return $this->current_page;
  }

  function getPageUri($page = null)
  {
    if ($page == null)
      $page = $this->page_counter;

    $params = $_GET;

    if ($page <= 1)
      unset($params[$this->id]);
    else
      $params[$this->id] = $page;

    $flat_params = array();
    $this->toFlatArray($params, $flat_params);

    $query_items = array();
    foreach ($flat_params as $key => $value)
      $query_items[] = $key . '=' . urlencode($value);

    $query = implode('&', $query_items);

    if (empty($query))
      return $this->base_url;
    else
      return $this->base_url . '?' . $query;
  }

  function toFlatArray($array, &$result, $prefix='')
  {
    foreach($array as $key => $value)
    {
      $string_key = ($prefix) ? '[' . $key . ']' : $key;

      if(is_array($value))
        $this->toFlatArray($value, $result, $prefix . $string_key);
      else
        $result[$prefix . $string_key] = $value;
    }
  }

  function getFirstPageUri()
  {
    return $this->getPageUri(1);
  }

  function getLastPageUri()
  {
    return $this->getPageUri($this->total_page_count);
  }
}


