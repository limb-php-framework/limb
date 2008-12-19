<?php
lmb_require('limb/fs/src/lmbFs.class.php');
lmb_require('limb/net/src/lmbMimeType.class.php');

class lmbCmsFileStorage
{
  protected $root_dir;

  function __construct($root_dir)
  {
    $this->root_dir = $root_dir;
    lmbFs :: mkdir($this->root_dir);
  }

  function storeFile($source, $mime_type = null)
  {
    if(!$mime_type)
      $mime_type = lmbMimeType :: getFileMimeType($source);

    if($mime_type == 'application/octet-stream')
      $mime_type = 'video/x-flv';

    $file_id = $this->_makeUniqueIdentifier($mime_type);

    $dest = $this->_getMediaFile($file_id);
    lmbFs :: cp($source, $dest);
    return $file_id;
  }

  function removeFile($file_id)
  {
    if($file = $this->getFilePath($file_id))
    {
      unlink($file);
      return true;
    }
    return false;
  }

  function getFilePath($file_id)
  {
    if(!file_exists($file = $this->_getMediaFile($file_id)))
       return null;

     return $file;
  }

  function hasFile($file_id)
  {
    return $this->getFilePath($file_id) !== null;
  }

  function getFileSize($file_id)
  {
    if(!$file_path = $this->getFilePath($file_id))
      return null;
    return filesize($file_path);
  }

  function getFileUrl($file_id)
  {
    $result = str_replace(strtolower($_SERVER['DOCUMENT_ROOT']), '',
                          strtolower(lmbFs :: normalizePath($this->getFilePath($file_id))));
    return '/' . ltrim($result, '/');
  }

  protected function _getMediaFile($file_id)
  {
    $md5 = md5($file_id);
    return $this->root_dir . '/' . $md5{0} . '/' . $file_id;
  }

  protected function _makeUniqueIdentifier($mime_type)
  {
    return uniqid() . '.' . lmbMimeType :: getExtension($mime_type);
  }
}

