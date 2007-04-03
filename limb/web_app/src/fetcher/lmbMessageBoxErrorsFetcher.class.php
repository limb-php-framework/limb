<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMessageBoxErrorsFetcher.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/fetcher/lmbFetcher.class.php');

class lmbMessageBoxErrorsFetcher extends lmbFetcher
{
  protected function _createDataSet()
  {
    $result = array();

    foreach(lmbToolkit :: instance()->getMessageBox()->getErrors() as $error)
    {
      if(!is_object($error))
        $result[] = new lmbDataspace(array('error' => $error));
      else
        $result[] = $error;
    }

    return new lmbArrayDataset($result);
  }
}
?>
