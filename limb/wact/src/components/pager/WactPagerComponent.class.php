<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * class WactPagerComponent.
 *
 * @package wact
 * @version $Id: WactPagerComponent.class.php 6734 2008-01-23 13:48:48Z serega $
 */
class WactPagerComponent extends WactRuntimeComponent
{
  protected $total_items = 0;
  protected $total_page_count = 0;
  protected $page_counter = 0;
  protected $displayed_page = 0;
  protected $items_per_page = 20;
  protected $pager_prefix = 'page';
  protected $base_url = null;
  protected $paged_dataset = null;

  protected $display_sections = true;
  protected $pages_per_section = 10;

  protected $display_elipses = false;
  protected $pages_in_middle = 5;
  protected $pages_in_sides = 3;

  function prepare()
  {
    if ($this->paged_dataset)
      $this->setTotalItems($this->paged_dataset->count());

    $this->total_page_count = ceil($this->total_items / $this->items_per_page);

    if ($this->total_page_count < 1)
      $this->total_page_count = 1;

    $this->_initBaseUrl();

    $this->page_counter = 1;
  }

  function resetPagesIterator()
  {
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

  function setPagerPrefix($prefix)
  {
    $this->pager_prefix = $prefix;
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

  //implementing WACT pager interface
  function getStartingItem()
  {
    $number = $this->getDisplayedPageBeginItem();
    return ($number == 0) ? 0 : $number - 1;
  }

  function setPagedDataSet($dataset)
  {
    $this->paged_dataset = $dataset;

    $this->prepare();
  }

  function getDisplayedPageBeginItem()
  {
    if($this->total_items < 1)
      return 0;

    return (int)($this->items_per_page * ($this->displayed_page - 1) + 1);
  }

  function getDisplayedPageEndItem()
  {
    $res = $this->items_per_page * $this->displayed_page;

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
    return ($this->displayed_page == 1);
  }

  function hasPrev()
  {
    return ($this->displayed_page > 1);
  }

  function hasNext()
  {
    return ($this->displayed_page < $this->total_page_count);
  }

  function isLast()
  {
    return ($this->displayed_page == $this->total_page_count);
  }

  protected function _initBaseUrl()
  {
    $this->base_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $pos = strpos($this->base_url, '?');
    if (is_integer($pos))
        $this->base_url = substr($this->base_url, 0, $pos);

    $this->displayed_page = (int)@$_GET[$this->getPagerId()];
    if (empty($this->displayed_page)) {
        $this->displayed_page = 1;
    }

    if (empty($this->displayed_page))
      $this->displayed_page = 1;

    if($this->displayed_page > $this->total_page_count)
      $this->displayed_page = $this->total_page_count;
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
    return $this->page_counter == $this->displayed_page;
  }

  function shouldDisplayPage()
  {
    if($this->display_sections)
      return $this->isDisplayedSection();

    $half_windows_size = ($this->pages_in_middle - 1) / 2;
    return (
        $this->page_counter <= $this->pages_in_sides ||
        $this->page_counter > $this->total_page_count - $this->pages_in_sides ||
        ($this->page_counter >= $this->displayed_page - $half_windows_size &&
        $this->page_counter <= $this->displayed_page + $half_windows_size) ||
        ($this->page_counter == $this->pages_in_sides + 1 &&
        $this->page_counter == $this->displayed_page - $half_windows_size - 1) ||
        ($this->page_counter == $this->total_page_count - $this->pages_in_sides &&
        $this->page_counter == $this->displayed_page + $half_windows_size + 1));
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
    return ceil($this->displayed_page / $this->pages_per_section);
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

  function getDisplayedPageUri()
  {
    return $this->getPageUri($this->displayed_page);
  }

  function getDisplayedPage()
  {
    return $this->displayed_page;
  }

  function getPagerId()
  {
    return $this->pager_prefix . '_' . $this->getId();
  }

  function getPageUri($page = null)
  {
    if ($page == null)
      $page = $this->page_counter;

    $params = $_GET;

    if ($page <= 1)
      unset($params[$this->getPagerId()]);
    else
      $params[$this->getPagerId()] = $page;

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


