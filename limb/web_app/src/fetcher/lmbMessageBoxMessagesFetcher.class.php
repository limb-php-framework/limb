<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbMessageBoxMessagesFetcher.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/web_app/src/fetcher/lmbFetcher.class.php');

class lmbMessageBoxMessagesFetcher extends lmbFetcher
{
  protected function _createDataSet()
  {
    $result = array();

    foreach(lmbToolkit :: instance()->getMessageBox()->getMessages() as $message)
      $result[] = array('message' => $message);

    return new lmbArrayDataset($result);
  }
}
?>
