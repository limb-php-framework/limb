<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/filter_chain/src/lmbInterceptingFilter.interface.php');

/**
 *  lmbFilterChain is an implementation of InterceptinfFilter design pattern.
 *
 *  lmbFilterChain contains registered filters and controls execution of the chain.
 *  Usually used as a FrontController in Limb based web applications (see web_app package)
 *
 *  lmbFilterChain can be an intercepting filter in its turn as well.
 *
 *  The best way to think about filters is as of a "russian nested doll", e.g:
 *  <code>
 *  // +-Filter A
 *  // | +-Filter B
 *  // | | +-Filter C
 *  // | | |_
 *  // | |_
 *  // |_
 *  </code>
 *  To achieve this sample structure you should write the following code:
 *  <code>
 *  $chain = new lmbFilterChain();
 *  $chain->registerFilter(new A());
 *  $chain->registerFilter(new B());
 *  $chain->registerFilter(new C());
 *  </code>
 *
 *  Remember, it's the filter that decides whether to pass control to the
 *  underlying filter, this is done by calling filter chain instance next()
 *  method.
 *
 *  Usage example:
 *  <code>
 *  lmb_require('limb/filter_chain/src/lmbFilterChain.class.php');
 *  //create new chain
 *  $chain = new lmbFilterChain();
 *  //register filter object in the chain
 *  $chain->registerFilter(new MyFilter());
 *  //register a handle for a filter in the chain
 *  //in this case we can avoid PHP code parsing if
 *  //this filter won't be processed
 *  $chain->registerFilter(new lmbHandle('/path/to/MyFilter'));
 *  //executes the chain
 *  $chain->process();
 *  </code>
 *
 * @version $Id: lmbFilterChain.class.php 7486 2009-01-26 19:13:20Z pachanga $
 * @package filter_chain
 */
class lmbFilterChain implements lmbInterceptingFilter
{
  /**
   * @var array registered filters (or filter handles (see {@link lmbHandle}))
   */
  protected $filters = array();
  /**
   * @var integer Index of the current active filter while running the chain
   */
  protected $counter = -1;

  function __construct(){}

  /**
   * Registers filter (or handle on a filter) in the chain.
   *
   * @return void
   */
  function registerFilter($filter)
  {
    $this->filters[] = $filter;
  }
  /**
   * Returns registered filters
   *
   * @return array
   */
  function getFilters()
  {
    return $this->filters;
  }
  /**
   * Runs next filter in the chain.
   *
   * @return void
   */
  function next()
  {
    $this->counter++;

    if(isset($this->filters[$this->counter]))
    {
      $this->filters[$this->counter]->run($this);
    }
  }
  /**
   * Executes the chain
   *
   * @return void
   */
  function process()
  {
    $this->counter = -1;
    $this->next();
  }
  /**
   * Implements lmbInterceptingFilter interface.
   * Filter chain can be an intercepting filter.
   *
   * @param object Filter chain instance
   * @return void
   */
  function run($filter_chain)
  {
    $this->process();
    $filter_chain->next();
  }
}


