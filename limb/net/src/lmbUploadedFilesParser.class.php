<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/net/src/lmbUploadedFile.class.php');

/**
 * class lmbUploadedFilesParser.
 *
 * @package net
 * @version $Id: lmbUploadedFilesParser.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbUploadedFilesParser
{
  function parse($files)
  {
    $result = array();

    foreach($files as $key => $chunk)
    {
      if($this->_isSimple($chunk))
        $result[$key] = $chunk;
      else
        $result[$key] = $this->_parseComplexChunk($chunk);
    }
    return $result;
  }

  function objectify($files)
  {
    return $this->_wrapWithObjects($this->parse($files));
  }

  protected function _isSimple($chunk)
  {
    if((isset($chunk['name']) && !is_array($chunk['name'])) &&
       (isset($chunk['error']) && !is_array($chunk['error'])) &&
       (isset($chunk['type']) && !is_array($chunk['type'])) &&
       (isset($chunk['size']) && !is_array($chunk['size'])) &&
       (isset($chunk['tmp_name']) && !is_array($chunk['tmp_name'])))
      return true;
    else
      return false;
  }

  function _wrapWithObjects($chunks)
  {
    $result = array();
    foreach($chunks as $key => $chunk)
    {
      if($this->_isSimple($chunk))
        $result[$key] = new lmbUploadedFile($chunk);
      else
        $result[$key] = $this->_wrapWithObjects($chunk);
    }
    return $result;
  }

  protected function _parseComplexChunk($chunk)
  {
    $result = array();
    foreach($chunk as $property_name => $data_set)
    {
      foreach($data_set as $arg_name => $value)
        $this->_parseRecursivePropertyValue($result[$arg_name], $property_name, $value);
    }
    return $result;
  }

  protected function _parseRecursivePropertyValue(&$result, $property_name, $data_set)
  {
    if(!is_array($data_set))
    {
      $result[$property_name] = $data_set;
      return;
    }

    foreach($data_set as $arg_name => $value)
    {
      $this->_parseRecursivePropertyValue($result[$arg_name], $property_name, $value);
    }
  }
}


