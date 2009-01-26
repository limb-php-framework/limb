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

class lmbSwishResult extends lmbCollection
{
  protected $index;
  protected $query;
  protected $total;
  protected $sort_params = array();
  protected $result_processor;
  protected $results;

  function __construct($index, $query)
  {
    parent :: __construct();
    $this->index = $index;
    $this->query = $query;
  }

  function _setupIteratedDataset()
  {
    if($this->iteratedDataset !== null)
      return;

    $swish = new Swish($this->index);
    $search = $swish->prepare();

    if($this->sort_params)      
    {
      foreach($this->sort_params as $key => $value)
        $search->setSort("$key $value");
    }

    $results = $search->execute($this->query);
    $this->total = $results->hits;

    $this->iteratedDataset= array();

    if($this->offset < 0 || $this->offset >= $this->total)
      return;
    elseif($this->offset)
      $results->seekResult($this->offset);

    $counter = 1;
    while($result = $results->nextResult())
    {
      if($this->limit && $counter++ > $this->limit)
        break;

      $decoded = array('docpath' => $result->swishdocpath,
                       'recnum' => $result->swishreccount,
                       'title' => $result->swishtitle,
                       'description' => $result->swishdescription,
                       'time' => date('Y-m-d', $result->swishlastmodified),
                       'stamp' => $result->swishlastmodified,
                       'rank' => $result->swishrank);


      if($this->result_processor)
        $this->iteratedDataset[] = call_user_func($this->result_processor, $decoded);
      else
        $this->iteratedDataset[] = $decoded;
    }
  }

  function setResultProcessor($processor)
  {
    $this->result_processor = $processor;
  }

  function add($item){}

  function at($index){}

  function sort($params)
  {
    $this->sort_params = $params;
    return $this;
  }

  function count()
  {
    if($this->total !== null)
      return $this->total;

    $swish = new Swish($this->index);
    $search = $swish->prepare();

    $results = $search->execute($this->query);
    $this->total = $results->hits;
    return $this->total;
  }
}


