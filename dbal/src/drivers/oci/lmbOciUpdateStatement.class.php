<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/dbal/src/drivers/lmbDbInsertStatement.interface.php');
lmb_require(dirname(__FILE__) . '/lmbOciManipulationStatement.class.php');

/**
 * class lmbOciUpdateStatement.
 *
 * @package dbal
 * @version $Id: lmbOciUpdateStatement.class.php 7486 2009-01-26 19:13:20Z pachanga $
 */
class lmbOciUpdateStatement extends lmbOciManipulationStatement
{
  protected function _mapHolderToField($name, $sql)
  {
    // a very basic implementation
    if(!preg_match("~\"?(\w+)\"?\s*=\s*:p_$name~i", $sql, $m))
      throw new lmbDbException("Could not map placeholder :p_$name to field in '$sql'");

    return strtolower(trim($m[1], '"'));
  }

  protected function _saveLobs()
  {
    $result = true;
    foreach($this->lobDescriptors as $name => $descriptor)
    {
      if(!$descriptor->truncate() || !$descriptor->save($this->lobs[$name]->read()))
      {
        $result = false;
        break;
      }
    }
    return $result;
  }
}


