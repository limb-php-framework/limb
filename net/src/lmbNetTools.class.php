<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/toolkit/src/lmbAbstractTools.class.php');
lmb_require('limb/net/src/lmbCurlRequest.class.php');
lmb_require('limb/net/src/lmbInputStreamParser.class.php');

/**
 * class lmbNetTools.
 *
 * @package net
 * @version $Id$
 */
class lmbNetTools extends lmbAbstractTools
{
  protected $response;
  protected $request;
  protected $curl_request;
  protected $input_stream_parser;

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

  function getCurlRequest()
  {
    if(!is_object($this->curl_request))
      $this->curl_request = new lmbCurlRequest();

    return $this->curl_request;
  }

  function setCurlRequest($curl_request)
  {
    $this->curl_request = $curl_request;
  }

  function getInputStreamParser()
  {
    if(!is_object($this->input_stream_parser))
      $this->input_stream_parser = new lmbInputStreamParser();

    return $this->input_stream_parser;
  }

  function setInputStreamParser($parser)
  {
    $this->input_stream_parser = $parser;
  }
}

