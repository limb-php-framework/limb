<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbOciUpdateStatement.class.php 4994 2007-02-08 15:36:08Z pachanga $
 * @package    dbal
 */
lmb_require('limb/dbal/src/drivers/lmbDbInsertStatement.interface.php');
lmb_require(dirname(__FILE__) . '/lmbOciManipulationStatement.class.php');

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

?>
