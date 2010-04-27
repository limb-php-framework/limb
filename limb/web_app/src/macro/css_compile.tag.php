<?php

lmb_require('limb/web_app/src/macro/file_version.tag.php');

/**
 * @tag css_compiled
 * @req_attributes dir
 * @restrict_self_nesting
 */  

class lmbCssCompiledMacroTag extends lmbFileVersionMacroTag
{  
  protected $_file_path = false;
  protected $_file_url = false;
  protected $_file_writed = false;

  protected function _generateContent($code)
  {
    $this->set('type', $this->has('type') ? $this->get('type') : 'css');
    parent :: _generateContent($code);
  }

  protected function _writeFile()
  {
    if($this->_file_writed)
      return;
    $this->_file_writed = true;

    $file = lmbFs :: normalizePath($this->get('src'), lmbFs :: UNIX);

    $abs_file = $this->getRootDir() . '/' . $file;
    if(!file_exists($abs_file)) 
    {
      if($this->getBool('safe', false))
      { 
        $this->_file_path = $abs_file;
        $this->_file_url = $this->get('src');
        return;
      }
      else
        throw new lmbMacroException('File '.$abs_file.' not found!');
    }

    $contents = file_get_contents($abs_file);
    $css_dir = dirname($file);
    if(preg_match_all('~url\(([^\)]+)\)~', $contents, $matches))  // simple. not support url('name (comment).jpg') or url('(").jpg') etc
    {
      $replaces = array();
      foreach($matches[1] as $key => $match)
      {
        $match = trim($match, '\'" ');
        $replaces[$matches[0][$key]] = 'url('.$this->addVersion(lmbFs :: normalizePath($css_dir.'/'.$match, lmbFs :: UNIX)).')';
      }
      $contents = str_replace(array_keys($replaces), array_values($replaces), $contents);
    }

    $url = lmbFs :: normalizePath(ltrim($this->get('dir') . '/' . str_replace('/', '-', $file), '/'));
    $path = $this->getRootDir() . '/' . $url;
    lmbFs :: safeWrite($path, trim($contents));

    $this->_file_path = $path;
    $this->_file_url = $url;
  }

  function getFilePath()
  {
    $this->_writeFile();
    return $this->_file_path;
  }

  function getFileUrl()
  {
    $this->_writeFile();
    return $this->_file_url;
  }
}
