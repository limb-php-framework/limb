<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: WactFilterInfoExtractor.class.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

class WactFilterInfoExtractor
{
  var $dictionary;
  var $file;
  var $annotations = array();

  function __construct($dict, $file)
  {
    $this->dictionary = $dict;
    $this->file = $file;
  }

  function setCurrentFile($file)
  {
    $this->file = $file;
  }

  function annotation($name, $value)
  {
    $this->annotations[$name] = $value;
  }

  function beginClass($class, $parent_class)
  {
    $this->_validate();

    if(isset($this->annotations['min_attributes']))
      $min = (int)$this->annotations['min_attributes'];
    else
      $min = 0;

    if(isset($this->annotations['max_attributes']))
      $max = (int)$this->annotations['max_attributes'];
    else
      $max = 0;

    $info = new WactFilterInfo($this->annotations['filter'], $class, $min, $max);

    $this->dictionary->registerFilterInfo($info, $this->file);
  }

  function endClass()
  {
    $this->annotations = array();
  }

  function _validate()
  {
    if(!file_exists($this->file))
      throw new WactException('File not found', array('file' => $this->file));

    if(!isset($this->annotations['filter']))
      throw new WactException('Annotation not found in file', array('annotation' => 'filter',
                                                                           'file' => $this->file));
  }
}
