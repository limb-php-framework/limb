<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id$
 * @package    wact
 */
require_once('limb/wact/src/components/iterator/WactBaseIteratorComponent.class.php');

class WactIteratorTransferComponent extends WactBaseIteratorComponent
{
  protected $dataset;

  function getDataset()
  {
    return $this->dataset;
  }

  function registerDataset($dataset)
  {
    $this->dataset = WactTemplate :: castToIterator($dataset);
  }
}
?>