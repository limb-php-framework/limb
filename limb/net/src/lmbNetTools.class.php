<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');

/**
 * class lmbNetTools.
 *
 * @package net
 * @version $Id: lmbNetTools.class.php 6243 2007-08-29 11:53:10Z pachanga $
 */
class lmbNetTools extends lmbAbstractTools
{
  protected $response;
  protected $request;

  function getRequest()
  {
    if(is_object($this->request))
      return $this->request;

    lmb_require('limb/net/src/lmbHttpRequest.class.php');
    $this->request = new lmbHttpRequest();

    return $this->request;
  }

  function setRequest($new_request)
  {
    $this->request = $new_request;
  }

  function getResponse()
  {
    if(is_object($this->response))
      return $this->response;

    lmb_require('limb/net/src/lmbHttpResponse.class.php');
    $this->response = new lmbHttpResponse();

    return $this->response;
  }

  function setResponse($response)
  {
    $this->response = $response;
  }
}

