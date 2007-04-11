<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbIni.class.php 5628 2007-04-11 12:09:20Z pachanga $
 * @package    config
 */
lmb_require('limb/util/src/exception/lmbFileNotFoundException.class.php');
lmb_require('limb/datasource/src/lmbSet.class.php');

class lmbIni extends lmbSet
{
  protected $file_path;

  function __construct($file)
  {
    $this->file_path = $file;
    $this->_load();
  }

  function getOverrideFile()
  {
    $file_name = substr($this->file_path, 0, strpos($this->file_path, '.ini'));
    $override_file_name = $file_name . '.override' . '.ini';

    if(file_exists($override_file_name))
      return $override_file_name;
    else
      return false;
  }

  // returns the file_path
  function getOriginalFile()
  {
    return $this->file_path;
  }

  protected function _load()
  {
    if(!file_exists($this->file_path))
      throw new lmbFileNotFoundException($this->file_path, 'ini file not found');
    $this->_parse($this->file_path);
  }

  protected function _parse()
  {
    $this->_parseFileContents($this->file_path);

    if($override_file = $this->getOverrideFile())
      $this->_parseFileContents($override_file);
  }

  protected function _parseFileContents($file_path)
  {
    $this->_parseLines(file($file_path));
  }

  protected function _parseLines($lines)
  {
    if($lines === false)
      throw new lmbException('lmbIni file is not found or could not be loaded', array('path' => $this->file_path));

    $current_group = null;

    if(count($lines) == 0)
      return false;

    foreach($lines as $line)
    {
      if(($line = trim($line)) == '')
        continue;
      // removing comments after #, not after # inside ""

      $line = preg_replace('/([^"#]+|"(.*?)")|(#[^#]*)/', "\\1", $line);
      // check for new group
      if(preg_match("#^\[(.+)\]\s*$#", $line, $new_group_name_array))
      {
        $new_group_name = trim($new_group_name_array[1]);
        $current_group = $this->_parseConstants($new_group_name);
        continue;
      }
      // check for variable
      if(preg_match("#^([a-zA-Z0-9_-]+)(\[([a-zA-Z0-9_-]*)\]){0,1}(\s*)=(.*)$#", $line, $value_array))
      {
        $var_name = trim($value_array[1]);

        $var_value = trim($value_array[5]);

        if(preg_match('/^"(.*)"$/', $var_value, $m))
          $var_value = $m[1];

        $var_value = $this->_parseConstants($var_value);

        if($value_array[2])//check for array []
        {
          if($value_array[3]) //check for hashed array, e.g ['test']
          {
            $key_name = $value_array[3];
            $this->_addArrayIniValue($var_name, $var_value, $key_name, $current_group);
          }
          else
            $this->_addArrayIniValue($var_name, $var_value, null, $current_group);
        }
        else
          $this->_addIniValue($var_name, $var_value, $current_group);
      }
    }
  }

  protected function _addIniValue($name, $value, $group = null)
  {
    if($group)
    {
      if(!$this->has($group))
        $this->set($group, array());

      $group_array = $this->get($group);
      $group_array[$name] = $value;
      $this->set($group, $group_array);
    }
    else
      $this->set($name, $value);
  }

  protected function _addArrayIniValue($name, $value, $index = null, $group = null)
  {
    if($group)
    {
      if(!$this->has($group))
        $this->set($group, array());

      $group_array = $this->get($group);

      if($index)
        $group_array[$name][$index] = $value;
      else
        $group_array[$name][] = $value;

      $this->set($group, $group_array);
    }
    else
    {
      if(!$this->has($name))
        $this->set($name, array());

      $array = $this->get($name);

       if($index)
         $array[$index] = $value;
       else
         $array[] = $value;

      $this->set($name, $array);
    }
  }

  protected function _parseConstants($value)
  {
    return preg_replace('~\{([^\}]+)\}~e', "constant('\\1')", $value);
  }

  function getOption($var_name, $group_name = null)
  {
    if($group_name)
    {
      if($group = $this->get($group_name))
      {
        if(is_array($group) && isset($group[$var_name]))
          return $group[$var_name];
      }
    }
    else
      return $this->get($var_name);
  }

  function assignOption(&$variable, $var_name, $group_name = null)
  {
    if(!$this->hasOption($var_name, $group_name))
      return false;

    $variable = $this->getOption($var_name, $group_name);
    return true;
  }

  function hasOption($var_name, $group_name = null)
  {
    if($group_name)
    {
      if($group = $this->get($group_name))
        return is_array($group) && array_key_exists($var_name, $group);
      else
        return false;
    }
    else
      return $this->has($var_name);
  }

  function hasGroup($group_name)
  {
    return is_array($this->get($group_name));
  }

  function getGroup($group_name)
  {
    return $this->get($group_name);
  }

  function getAll()
  {
    return $this->export();
  }

  function mergeWith($ini)
  {
    $clone = clone($this);
    $clone->merge($ini->export());
    return $clone;
  }
}

?>
