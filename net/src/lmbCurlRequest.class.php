<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbCurlRequest.
 *
 * @package net
 * @version $Id$
 */
class lmbCurlRequest
{
  protected $_url = '';
  protected $_handle;
  protected $_opts = array();

  protected $_error;
  protected $_request_status;

  function __construct($url = '')
  {
    if('' != $url)
      $this->setUrl($url);

    $this->_initDefaultOptions();
  }

  function setUrl($url)
  {
    $this->_url = $url;
    $this->setOpt(CURLOPT_URL, $url);
  }

  function setTimeout($timeout)
  {
    $this->setOpt(CURLOPT_TIMEOUT_MS, $timeout);
  }

  function open($data = null, $request_method = null)
  {
    $this->_ensureCurl();

    //-- this operation is added for backward compatibility
    if(!is_null($data) && is_null($request_method))
      $this->_setupPostRequest($data);

    else if('POST' == $request_method)
      $this->_setupPostRequest($data);

    else if('PUT' == $request_method)
      $this->_setupPutRequest($data);

    else if('DELETE' == $request_method)
      $this->_setupDeleteRequest();

    $this->_setupCurlOptions();

    return $this->_exec();
  }

  protected function _setupPostRequest($data)
  {
    $this->setOpt(CURLOPT_POST, 1);

    if(!is_null($data))
      $this->setOpt(CURLOPT_POSTFIELDS, $data);
  }

  protected function _setupPutRequest($data)
  {
    $data_str = "";
    if(!is_null($data))
    {
      foreach($data as $key => $value)
        $data_str .= "$key=$value&";
    }

    $fh = fopen('php://temp', 'rw');
    fwrite($fh, $data_str);
    rewind($fh);

    $this->setOpt(CURLOPT_PUT, 1);
    $this->setOpt(CURLOPT_INFILE, $fh);
    $this->setOpt(CURLOPT_INFILESIZE, strlen($data_str));
    $this->setOpt(CURLOPT_HTTPHEADER, Array('Expect: '));
  }

  protected function _setupDeleteRequest()
  {
    $this->setOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');
  }

  function setOpt($opt, $value)
  {
    $this->_opts[$opt] = $value;
  }

  protected function _initDefaultOptions()
  {
    $this->_opts = array(
      CURLOPT_HEADER => 0,
      CURLOPT_RETURNTRANSFER => 1,
    );
  }

  protected function _ensureCurl()
  {
    if(!is_resource($this->_handle))
      $this->_handle = curl_init();
  }

  protected function _setupCurlOptions()
  {
    foreach($this->_opts as $opt => $value)
      curl_setopt($this->_handle, $opt, $value);
  }

  protected function _exec()
  {
    $res = curl_exec($this->_handle);

    $this->_request_status = curl_getinfo($this->_handle, CURLINFO_HTTP_CODE);
    $this->_error = curl_error($this->_handle);

    $this->_resetCurl();

    return $res;
  }

  protected function _resetCurl()
  {
    if(is_resource($this->_handle))
      curl_close($this->_handle);

    $this->_opts = array();
  }

  function getError()
  {
    return $this->_error;
  }

  function getRequestStatus()
  {
    return $this->_request_status;
  }
}
