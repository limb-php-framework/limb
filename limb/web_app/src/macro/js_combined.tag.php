<?php

lmb_require('limb/web_app/src/macro/file_version.tag.php');

/**
 * @tag js:combined
 * @aliases js_combined
 * @req_attributes dir
 * @restrict_self_nesting
 */  

class lmbJsCombinedMacroTag extends lmbFileVersionMacroTag
{  
  protected $_file_path = false;
  protected $_file_url = false;
  protected $_file_writed = false;

  protected function _generateContent($code)
  {
    $this->set('type', $this->has('type') ? $this->get('type') : 'js');
    parent :: _generateContent($code);
  }

  protected function _writeFile()
  {
    if($this->_file_writed)
      return;
    
    $files = array();
    $join_contents = '';
    foreach($this->children as $child)
    {
      if($child instanceof lmbJsRequireOnceMacroTag)
      {
        $file = $child->getFilePath();
        if(!is_file($file) || !realpath($file) || null === ($content = file_get_contents($file)))
        {
          if($child->getBool('safe'))
          {
            $join_contents .= "\n/* include ".basename($file)." - NOT FOUND */\n";
            continue;
          }
          else
            throw new lmbMacroException('File "' . $file . '" not found in '.$this->getRootDir().', src: "' . $child->get('src') . '"');
        }
        $file = lmbFs :: normalizePath(realpath($file));
        if(!in_array($file, $files))
        {
          $files[] = $file;
          $join_contents .= "\n/* include ".basename($file)." */\n" . $content;
        }
      }
    }
    sort($files, SORT_STRING);
    $url = lmbFs :: normalizePath(ltrim($this->get('dir') . '/' . md5(implode("\n", $files)).'.js', '/'));
    $path = $this->getRootDir() . '/' . $url;
    lmbFs :: safeWrite($path, trim($join_contents));

    $this->_file_path = $path;
    $this->_file_url = $url;
    $this->_file_writed = true;
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
