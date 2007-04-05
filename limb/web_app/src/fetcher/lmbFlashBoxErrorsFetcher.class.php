<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFlashBoxErrorsFetcher.class.php 5532 2007-04-05 10:31:47Z pachanga $
 * @package    web_app
 */
lmb_require('limb/datasource/src/lmbArrayDataset.class.php');
lmb_require('limb/web_app/src/fetcher/lmbFetcher.class.php');

class lmbFlashBoxErrorsFetcher extends lmbFetcher
{
  protected function _createDataSet()
  {
    $result = array();

    $flash_box = lmbToolkit :: instance()->getFlashBox();
    foreach($flash_box->getErrors() as $error)
      $result[] = array('message' => $error,
                        'text' => $error // for BC
                        );

    $flash_box->resetErrors();

    return new lmbArrayDataset($result);
  }
}
?>
