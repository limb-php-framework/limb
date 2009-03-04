<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
require_once('limb/wact/src/components/WactClassPath.class.php');
require_once('limb/wact/src/components/iterator/WactBaseIteratorComponent.class.php');

/**
 * class WactFetchComponent.
 *
 * @package wact
 * @version $Id$
 */
class WactFetchComponent extends WactBaseIteratorComponent
{
  protected $fetcher_name;
  protected $include_path;
  protected $params = array();
  protected $dataset = null;
  protected $cache_dataset = true;
  protected $reserved_params = array('limit', 'offset', 'order');

  function setFetcherName($fetcher_name)
  {
    $this->fetcher_name = $fetcher_name;
  }

  function setIncludePath($include_path)
  {
    $this->include_path = $include_path;
  }

  function setAdditionalParam($param, $value)
  {
    $this->params[$param] = $value;
  }

  function setCacheDataset($flag)
  {
    $this->cache_dataset = $flag;
  }

  function getDataset()
  {
    if($this->dataset !== null && (boolean)$this->cache_dataset)
      return $this->dataset;

    $fetcher = $this->_createFetcher();

    $this->dataset = $fetcher->fetch();

    return $this->dataset;
  }

  protected function _createFetcher()
  {
    $class_path = new WactClassPath($this->fetcher_name, $this->include_path);
    $fetcher = $class_path->createObject();

    foreach($this->params as $param => $value)
    {
      $method = WactTemplate :: toStudlyCaps('set_' . $param, false);
      if(in_array($param, $this->reserved_params))
        $this->$method($value);
      elseif(method_exists($fetcher, $method))
        $fetcher->$method($value);
      else
        throw new WactException('Fetcher "' .$this->fetcher_name. '" does not support method: '. $method);
    }

    return $fetcher;
  }
}

