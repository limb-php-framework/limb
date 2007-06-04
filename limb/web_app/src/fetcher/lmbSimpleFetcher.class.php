<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbSimpleFetcher.class.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
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
