<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/web_app/src/fetcher/lmbFetcher.class.php');

class lmbSimpleFetcher extends lmbFetcher
{
  protected $dataset_name;

  function setDatasetName($dataset_name)
  {
    $this->dataset_name = $dataset_name;
  }

  protected function _createDataSet()
  {
    $class_path = new lmbClassPath($this->dataset_name);
    return $class_path->createObject();
  }
}
?>
