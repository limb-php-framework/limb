<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbIni.class.php 5423 2007-03-29 13:09:55Z pachanga $
 * @package    config
 */
lmb_require('limb/util/src/util/lmbComplexArray.class.php');
lmb_require('limb/util/src/exception/lmbFileNotFoundException.class.php');

class lmbIni
{
  protected $group_values;
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

    $current_group = 'default';

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

        if(!isset($this->group_values[$current_group]))
          $this->group_values[$current_group] = array();
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
          if($value_array[3]) //check for hashed array ['test']
          {
            $key_name = $value_array[3];

            if(isset($this->group_values[$current_group][$var_name]) &&
                is_array($this->group_values[$current_group][$var_name]))
              $this->group_values[$current_group][$var_name][$key_name] = $var_value;
            else
              $this->group_values[$current_group][$var_name] = array($key_name => $var_value);
          }
          else
          {
            if(isset($this->group_values[$current_group][$var_name]) &&
                is_array($this->group_values[$current_group][$var_name]))
              $this->group_values[$current_group][$var_name][] = $var_value;
            else
              $this->group_values[$current_group][$var_name] = array($var_value);
          }
        }
        else
        {
          $this->group_values[$current_group][$var_name] = $var_value;
        }
      }
    }
  }

  protected function _parseConstants($value)
  {
    return preg_replace('~\{([^\}]+)\}~e', "constant('\\1')", $value);
  }

  function get($name)
  {
    return $this->getOption($name);
  }

  function getOption($var_name, $group_name = 'default')
  {
    if(!isset($this->group_values[$group_name]))
      return;

    if(isset($this->group_values[$group_name][$var_name]))
      return $this->group_values[$group_name][$var_name];

    return;
  }

  function assignOption(&$variable, $var_name, $group_name = 'default')
  {
    if(!$this->hasOption($var_name, $group_name))
      return false;

    $variable = $this->getOption($var_name, $group_name);
    return true;
  }

  function hasOption($var_name, $group_name = 'default')
  {
    return isset($this->group_values[$group_name][$var_name]);
  }

  function hasGroup($group_name)
  {
    return isset($this->group_values[$group_name]);
  }

  function getGroup($group_name)
  {
    if(isset($this->group_values[$group_name]))
      return $this->group_values[$group_name];
  }

  function getAll()
  {
    return $this->group_values;
  }

  function mergeWith($ini)
  {
    $clone = clone($this);
    $clone->group_values = lmbComplexArray :: arrayMerge($clone->group_values, $ini->getAll());
    return $clone;
  }
}

?>
