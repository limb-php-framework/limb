<?php
class lmbCmsAdminFilterHelper
{
  protected $toolkit;
  protected $request;
  protected $session;
  protected $filter_name;

  function __construct($filter_name)
  {
    $this->filter_name = $filter_name;
    $this->toolkit = lmbToolkit :: instance();
    $this->request = $this->toolkit->getRequest();
    $this->session = $this->toolkit->getSession();
  }
  
  function getFilter($param_name)
  {
    $params = $this->session->get($this->filter_name, array());
    if(isset($params[$param_name]))
      return $params[$param_name];
  }

  function setFilter($param_name, $default_value = null)
  {
    $params = $this->session->get($this->filter_name, array());

    if(!$this->request->has($param_name))
    {
      if(isset($params[$param_name]))
        $value = $params[$param_name];
      else
        $value = $default_value;

      $this->request->set($param_name, $value);
    }
    else
      $value = $this->request->get($param_name);

    $params[$param_name] = $value;

    $this->session->set($this->filter_name, $params);
  }

  function reset()
  {
     $this->session->set($this->filter_name, array());
  }
}


