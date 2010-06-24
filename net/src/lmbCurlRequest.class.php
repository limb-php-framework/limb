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
  protected $url = '';
  protected $handle;
  protected $opts = array();

  function __construct($url)
  {
    $this->url = $url;
    $this->_initDefaultOptions();
  }

  function open($post_data = '')
  {
    $this->_ensureCurl();
    if($post_data)
      $this->_setPostData($post_data);

    $this->_setupCurlOptions();
    return $this->_exec();
  }

  function setOpt($opt, $value)
  {
    $this->opts[$opt] = $value;
  }

  protected function _initDefaultOptions()
  {
    $this->opts = array(CURLOPT_HEADER => 0,
                        CURLOPT_RETURNTRANSFER => 1,
                        CURLOPT_URL => $this->url);
  }

  protected function _ensureCurl()
  {
    if(!is_resource($this->handle))
     $this->handle = curl_init();
  }

  protected function _setupCurlOptions()
  {
    foreach($this->opts as $opt => $value)
      curl_setopt($this->handle, $opt, $value);
  }

  protected function _exec()
  {
    $res = curl_exec($this->handle);
    $this->_resetCurl();
    return $res;
  }

  protected function _resetCurl()
  {
    if(is_resource($this->handle))
      curl_close($this->handle);
    $this->opts = array();
  }

  protected function _setPostData($post_data)
  {
    if(!$post_data)
      return;

    $this->setOpt(CURLOPT_POST, 1);

    $var_string = '';
    foreach ($post_data as $k => $v)
      if(is_array($v))
      {
        foreach($v as $value)
        $var_string .= "{$k}[]={$value}&";
      }
      else
        $var_string .= "{$k}={$v}&";

    $this->setOpt(CURLOPT_POSTFIELDS, $var_string);
  }
}


