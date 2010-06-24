<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
require_once('limb/wact/src/components/iterator/WactBaseIteratorComponent.class.php');

/**
 * class WactIteratorTransferComponent.
 *
 * @package wact
 * @version $Id$
 */
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

