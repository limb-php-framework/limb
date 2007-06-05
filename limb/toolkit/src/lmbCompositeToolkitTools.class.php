<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
* Composes several tools into one
* Doesn't check if tools have any intersecting methods. Method of the latter tools always wins.
* @see lmbToolkit :: merge()
*/
class lmbCompositeToolkitTools implements lmbToolkitTools
{
  /**
  * @var array Array of {@link lmbToolkitTools}
  */
  protected $tools = array();

  /**
  * Constructor
  * Can accept array of tools or many arguments. In second case will treat all arguments as tools
  * @param array Array of {@link lmbToolkitTools}
  */
  function __construct($tools)
  {
    if(is_array($tools))
      $this->tools = $tools;
    else
      $this->tools = func_get_args();
  }

  function __clone()
  {
    foreach($this->tools as $key => $tools)
      $this->tools[$key] = clone($tools);
  }

  /**
  * @see lmbToolkitTools :: getToolsSignatures()
  */
  function getToolsSignatures()
  {
    $result = array();
    foreach($this->tools as $tools)
    {
      $signatures = $tools->getToolsSignatures();
      $result = array_merge($result, $signatures);
    }
    return $result;
  }
}
?>
