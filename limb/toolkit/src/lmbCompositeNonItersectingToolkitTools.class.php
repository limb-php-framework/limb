<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCompositeNonItersectingToolkitTools.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */
lmb_require(dirname(__FILE__) . '/lmbCompositeToolkitTools.class.php');

/**
* Composes several tools into one
* Checks if tools have intersecting methods. Throws an exception if two separate tools have the same method.
* @see lmbToolkit :: extend()
*/
class lmbCompositeNonItersectingToolkitTools extends lmbCompositeToolkitTools
{
  function getToolsSignatures()
  {
    $result = array();
    foreach($this->tools as $tools)
    {
      $signatures = $tools->getToolsSignatures();

      if($intersect = array_intersect(array_keys($signatures), array_keys($result)))
      {
        throw new lmbException('tools signatures intersection',
                                array('intersection' => $intersect));
      }
      $result = array_merge($result, $signatures);
    }
    return $result;
  }
}
?>
