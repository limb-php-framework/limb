<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbSys.class.php');
lmb_require('limb/fs/src/exception/lmbFsException.class.php');

/**
 * class lmbFs.
 *
 * @package fs
 * @version $Id$
 */
class lmbFs
{
  const LOCAL = 1;
  const UNIX = 2;
  const DOS = 3;
  const WIN32_NET_PREFIX = '\\\\';

  static function safeWrite($file, $content, $perm=0664)
  {
    self :: mkdir(dirname($file));

    $tmp = self :: generateTmpFile('_');
    $fh = fopen($tmp, 'w');

    if($fh === false)
    {
      @unlink($tmp);
      throw new lmbFsException('could not open file for writing', array('file' => $file));
    }

    //just for safety
    @flock($fh, LOCK_EX);
    fwrite($fh, $content);
    @flock($fh, LOCK_UN);
    fclose($fh);

    if(lmbSys :: isWin32() && file_exists($file))
      @unlink($file);

    if(!@rename($tmp, $file))
    {
      @unlink($tmp);
      throw new lmbFsException('could not move file', array('src' => $tmp, 'file' => $file));
    }

    @chmod($file, $perm);
    if(file_exists($tmp))
      @unlink($tmp);
  }

  static function getTmpDir()
  {
    if(defined('LIMB_VAR_DIR'))
      return LIMB_VAR_DIR;

    if($path = session_save_path())
    {
      if(($pos = strpos($path, ';')) !== false)
        $path = substr($path, $pos+1);
      return $path;
    }

    if($tmp = getenv('TMP') || $tmp = getenv('TEMP') || $tmp = getenv('TMPDIR'))
      return $tmp;

    //gracefull falback?
    return '/tmp';
  }

  static function generateTmpFile($prefix = 'p')
  {
    return tempnam(self :: getTmpDir(), $prefix);
  }

  /**
   * @deprecated
   */
  static function generateTempFile($prefix = 'p')
  {
    return self :: generateTmpFile($prefix);
  }

  static function dirpath($path)
  {
    $path = self :: normalizePath($path);

   if(($dir_pos = strrpos($path, self :: separator())) !== false )
      return substr($path, 0, $dir_pos);

    return $path;
  }

  /**
  * Creates the directory $dir with permissions $perm.
  * If $parents is true it will create any missing parent directories,
  * just like 'mkdir -p'.
  */
  static function mkdir($dir, $perm=0777, $parents=true)
  {
    if(is_dir($dir))
      return;

    $dir = self :: normalizePath($dir);

    if(!$parents)
    {
      self :: _doMkdir($dir, $perm);
      return;
    }

    $separator = self :: separator();
    $path_elements = self :: explodePath($dir);

    if(count($path_elements) == 0)
      return;

    $index = self :: _getFirstExistingPathIndex($path_elements, $separator);

    if($index === false)
    {
      throw new lmbFsException('cant find first existent path', array('dir' => $dir));
    }

    $offset_path = '';
    for($i=0; $i < $index; $i++)
    {
      $offset_path .= $path_elements[$i] . $separator;
    }

    for($i=$index; $i < count($path_elements); $i++)
    {
      $offset_path .= $path_elements[$i] . $separator;
      self :: _doMkdir($offset_path, $perm);
    }
  }

  protected static function _getFirstExistingPathIndex($path_elements, $separator)
  {
    for($i=count($path_elements); $i > 0; $i--)
    {
      $path = implode($separator, $path_elements);

      if(is_dir($path))
        return $i;

      array_pop($path_elements);
    }

    if(self :: isPathAbsolute($path))
      return false;
    else
      return 0;
  }

  /**
  * Creates the directory $dir with permission $perm.
  */
  protected static function _doMkdir($dir, $perm)
  {
    if(is_dir($dir))
      return;

    if(self :: _hasWin32NetPrefix($dir))
      return;

    $oldumask = umask(0);
    if(!mkdir($dir, $perm))
    {
      umask($oldumask);
      throw new lmbFsException('failed to create directory', array('dir' => $dir));
    }

    umask($oldumask);
  }

  static function explodePath($path, $fs_type = self :: UNIX)
  {
    $path = self :: normalizePath($path, $fs_type);
    $separator = self :: separator($fs_type);

    $dir_elements = explode($separator, $path);

    if(sizeof($dir_elements) > 1 &&  $dir_elements[sizeof($dir_elements)-1] === '')
      array_pop($dir_elements);

    if(self :: _hasWin32NetPrefix($path))
    {
      array_shift($dir_elements);
      array_shift($dir_elements);
      $dir_elements[0] = self :: WIN32_NET_PREFIX . $dir_elements[0];
    }
    return $dir_elements;
  }

  static function joinPath($arr, $fs_type = self :: UNIX)
  {
    return implode(self :: separator($fs_type), $arr);
  }

  static function chop($path)
  {
    if(substr($path, -1) == '/' || substr($path, -1) == '\\')
      $path = substr($path, 0, -1);

    return $path;
  }

  static function rm($file)
  {
    if(!file_exists($file))
      return false;

    self :: _doRm(self :: normalizePath($file), self :: separator());
    clearstatcache();

    return true;
  }

  protected static function _doRm($item, $separator)
  {
    if(!is_dir($item))
    {
      if(!@unlink($item))
        throw new lmbFsException('failed to remove file', array('file' => $item));
      return;
    }

    if(!$handle = @opendir($item))
      throw new lmbFsException('failed to open directory', array('dir' => $item));

    while(($file = readdir($handle)) !== false)
    {
      if($file == '.' ||  $file == '..')
        continue;

      self :: _doRm($item . $separator . $file, $separator);
    }

    closedir($handle);

    if(!@rmdir($item))
      throw new lmbFsException('failed to remove directory', array('dir' => $item));
  }

  static function mv($src, $dest)
  {
    if(is_dir($src) || is_file($src))
    {
      if(!@rename($src, $dest))
        throw new lmbFsException('failed to move item', array('src' => $src, 'dest' => $dest));

      clearstatcache();
    }
    else
      throw new lmbFsException('source file or directory does not exist', array('src' => $src));
  }

  static function cp($src, $dest, $exclude_regex = '', $include_regex = '', $as_child = false, $include_hidden = true)
  {
    if(!is_dir($src))
    {
      if(!is_dir($dest))
        self :: mkdir(dirname($dest));
      else
        $dest = $dest . '/' . basename($src);

      if(@copy($src, $dest) === false)
        throw new lmbFsException('failed to copy file', array('src' => $src, 'dest' => $dest));
      return;
    }

    self :: mkdir($dest);

    $src = self :: normalizePath($src);
    $dest = self :: normalizePath($dest);
    $separator = self :: separator();

    if($as_child)
    {
      $separator_regex = preg_quote($separator);
      if(preg_match( "#^.+{$separator_regex}([^{$separator_regex}]+)$#", $src, $matches))
      {
        self :: _doMkdir($dest . $separator . $matches[1], 0777);
        $dest .= $separator . $matches[1];
      }
      else
        return false;
    }
    $items = self :: find($src, 'df', $include_regex, $exclude_regex, false, $include_hidden);

    $total_items = $items;
    while(count($items) > 0)
    {
      $current_items = $items;
      $items = array();
      foreach($current_items as $item)
      {
        $full_path = $src . $separator . $item;
        if(is_file( $full_path))
          copy($full_path, $dest . $separator . $item);
        elseif(is_dir( $full_path))
        {
          self :: _doMkdir($dest . $separator . $item, 0777);

          $new_items = self :: find($full_path, 'df', $include_regex, $exclude_regex, $item, $include_hidden);

          $items = array_merge($items, $new_items);
          $total_items = array_merge($total_items, $new_items);

         unset($new_items);
        }
      }
    }
    if($total_items)
      clearstatcache();

    return $total_items;
  }

  static function ls($path)
  {
    if(!is_dir($path))
      return array();

    $files = array();
    $path = self :: normalizePath($path);
    if($handle = opendir($path))
    {
      while(($file = readdir($handle)) !== false)
      {
        if($file != '.' &&  $file != '..' )
        {
          $files[] = $file;
        }
      }
      closedir($handle);
    }
    return $files;
  }

  /**
  * Return the separator used between directories and files according to $type.
  */
  static function separator($type = lmbFs :: UNIX)
  {
    switch(self :: _concreteSeparatorType($type))
    {
      case self :: UNIX:
        return '/';
      case self :: DOS:
        return "\\";
    }
  }

  protected static function _concreteSeparatorType($type)
  {
    if($type == self :: LOCAL)
    {
      if(lmbSys :: isWin32())
        $type = self :: DOS;
      else
        $type = lmbFs :: UNIX;
    }
    return $type;
  }

  /**
  * Converts any directory separators found in $path, in both unix and dos style, into
  * the separator type specified by $to_type and returns it.
  */
  static function convertSeparators($path, $to_type = self :: UNIX)
  {
    $separator = self :: separator($to_type);
    return preg_replace("#[/\\\\]#", $separator, $path);
  }

  /**
  * Removes all unneeded directory separators and resolves any "."s and ".."s found in $path.
  *
  * For instance: "var/../lib/db" becomes "lib/db", while "../site/var" will not be changed.
  * Will also convert separators
  */
  static function normalizePath($path, $to_type = self :: UNIX)
  {
    $path = self :: convertSeparators($path, $to_type);
    $separator = self :: separator($to_type);

    $path = self :: _normalizeSeparators($path, $separator);

    $path_elements= explode($separator, $path);
    $newpath_elements= array();

    foreach($path_elements as $path_element)
    {
      if($path_element == '.')
        continue;
      if($path_element == '..' &&
          count($newpath_elements) > 0)
        array_pop($newpath_elements);
      else
        $newpath_elements[] = $path_element;
    }
    if(count( $newpath_elements) == 0)
      $newpath_elements[] = '.';

    $path = implode($separator, $newpath_elements);
    return rtrim($path, '/\\');
  }

  static function isPathRelative($path, $fs_type = self :: LOCAL)
  {
    return !self :: isPathAbsolute($path, $os_type);
  }

  static function isPathAbsolute($path, $fs_type = self :: LOCAL)
  {
    switch(self :: _concreteSeparatorType($fs_type))
    {
      case self :: UNIX:
        return $path{0} == '/';
      case self :: DOS:
        return $path{0} == '/' ||
               $path{0} == "\\" ||
               preg_match('~^[a-zA-Z]+:~', $path);
    }
  }

  protected static function _normalizeSeparators($path, $separator)
  {
    $quoted = preg_quote($separator);
    $clean_path = preg_replace( "~$quoted$quoted+~", $separator, $path);

    if(self :: _hasWin32NetPrefix($path))
      $clean_path = '\\' . $clean_path;

    return $clean_path;
  }

  protected static function _hasWin32NetPrefix($path)//ugly!!!
  {
    if(lmbSys :: isWin32() &&  strlen($path) > 2)
    {
      return (substr($path, 0, 2) == self :: WIN32_NET_PREFIX);
    }
    return false;
  }

  static function path($names, $include_end_separator=false, $type = self :: UNIX)
  {
    $separator = self :: separator($type);
    $path = implode($separator, $names);
    $path = self :: normalizePath($path, $type);

    $has_end_separator = (strlen($path) > 0 &&  $path[strlen($path) - 1] == $separator);

   if($include_end_separator &&  !$has_end_separator)
      $path .= $separator;
    elseif (!$include_end_separator &&  $has_end_separator)
      $path = substr($path, 0, strlen($path) - 1);

    return $path;
  }

  static function find($dir, $types = 'dfl', $include_regex = '', $exclude_regex = '', $add_path = true, $include_hidden = false)
  {
    $dir = self :: normalizePath($dir);
    $dir = self :: chop($dir);

    $items = array();

    $separator = self :: separator();

    if($handle = @opendir($dir))
    {
      while(($element = readdir($handle)) !== false)
      {
        if($element == '.' || $element == '..')
         continue;
        if(!$include_hidden && $element[0] == '.')
         continue;
        if($include_regex && !preg_match($include_regex, $element, $m))
         continue;
        if($exclude_regex && preg_match($exclude_regex, $element, $m))
         continue;
        if(is_dir($dir . $separator . $element) && strpos($types, 'd') === false)
         continue;
        if(is_link($dir . $separator . $element) && strpos($types, 'l') === false)
         continue;
        if(is_file( $dir . $separator . $element ) && strpos($types, 'f') === false)
         continue;

      if($add_path)
      {
       if(is_string($add_path))
         $items[] = $add_path . $separator . $element;
       else
        $items[] = $dir . $separator . $element;
      }
      else
        $items[] = $element;
      }
      closedir($handle);
    }
    sort($items);
    return $items;
  }

  static function findRecursive($path, $types = 'dfl', $include_regex = '', $exclude_regex = '', $add_path = true, $include_hidden = false)
  {
    return self :: walkDir($path,
                          array('lmbFs', '_doFindRecursive'),
                          array('types' => $types,
                               'include_regex' => $include_regex,
                               'exclude_regex' => $exclude_regex,
                               'add_path' => $add_path,
                               'include_hidden' => $include_hidden),
                          true);
  }

  protected static function _doFindRecursive($dir, $file, $path, $params, &$return_params)
  {
    if(!is_dir($path))
      return;

    $items = self :: find($path,
                          $params['types'],
                          $params['include_regex'],
                          $params['exclude_regex'],
                          $params['add_path'],
                          $params['include_hidden']);
    foreach($items as $item)
      $return_params[] = $item;
  }

  static function walkDir($dir, $function_def, $params=array(), $include_first=false)
  {
    $return_params = array();

    $separator = self :: separator();
    $dir = self :: normalizePath($dir);
    $dir = self :: chop($dir);

    $params['separator'] = $separator;

    self :: _doWalkDir($dir,
                     $separator,
                     $function_def,
                     $return_params,
                     $params,
                     $include_first);

    return $return_params;
  }

  protected static function _doWalkDir($item, $separator, $function_def, &$return_params, $params, $include_first, $level=0)
  {
    if($level > 0 || ($level == 0 && $include_first))
      call_user_func_array($function_def, array('dir' => dirname($item),
                                                'file' => basename($item),
                                                'path' => $item,
                                                'params' => $params,
                                                'return_params' => &$return_params));
    if(!is_dir($item))
      return;

    $handle = opendir($item);

    while(($file = readdir($handle)) !== false)
    {
     if(($file == '.') || ($file == '..'))
        continue;

      self :: _doWalkDir($item . $separator . $file,
                       $separator,
                       $function_def,
                       $return_params,
                       $params,
                       $level + 1);
    }
    closedir($handle);
  }
}

