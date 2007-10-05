<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * Base class for runtime components that hold data
 * @package wact
 * @version $Id: WactDatasourceRuntimeComponent.class.php 6386 2007-10-05 14:22:21Z serega $
 */
class WactDatasourceRuntimeComponent extends WactRuntimeComponent
{
  public $datasource;

  function __construct($id)
  {
    parent :: __construct($id);

    $this->datasource = array();
  }

  function set($field, $value)
  {
    WactTemplate :: setValue($this->datasource, $field, $value);
  }

  function get($field)
  {
    return WactTemplate :: getValue($this->datasource, $field);
  }

  function registerDataSource($datasource)
  {
    $this->datasource = $datasource;
  }

  function getDataSource()
  {
    return $this->datasource;
  }
}


