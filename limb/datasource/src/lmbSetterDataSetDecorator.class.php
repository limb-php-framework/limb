<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSetterDataSetDecorator.class.php 5386 2007-03-28 12:56:31Z pachanga $
 * @package    datasource
 */
lmb_require('limb/datasource/src/lmbPagedDatasetDecorator.class.php');

class lmbSetterDataSetDecorator extends lmbPagedDatasetDecorator
{
  protected $properties = array();

  function current()
  {
    $record = parent :: current();
    foreach($this->properties as $name => $value)
      $record->set($name, $value);
    return $record;
  }

  function __call($method, $args)
  {
    if(strpos('set', $method) != 0)
      return;

    $prop_name = lmb_under_scores(substr($method, 3));
    $this->properties[$prop_name] = $args[0];
  }
}

?>