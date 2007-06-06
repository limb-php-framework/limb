<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require(dirname(__FILE__) . '/lmbCompositeToolkitTools.class.php');

/**
 * Composes several tools into one
 * Checks if tools have intersecting methods. Throws an exception if two separate tools have the same method.
 * @see lmbToolkit :: extend()
 * @package toolkit
 * @version $Id: lmbCompositeNonItersectingToolkitTools.class.php 5945 2007-06-06 08:31:43Z pachanga $
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
