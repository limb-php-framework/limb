<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFlashBoxFetcher.class.php 5532 2007-04-05 10:31:47Z pachanga $
 * @package    web_app
 */
lmb_require('limb/datasource/src/lmbArrayDataset.class.php');
lmb_require('limb/web_app/src/fetcher/lmbFetcher.class.php');

class lmbFlashBoxFetcher extends lmbFetcher
{
  protected function _createDataSet()
  {
    $result = array();
    $flash_box = lmbToolkit :: instance()->getFlashBox();

    foreach($flash_box->getErrors() as $error)
      $result[] = array('message' => $error, 'is_error' => true, 'is_message' => false,
                        'text' => $error // for BC
                        );


    foreach($flash_box->getMessages() as $message)
      $result[] = array('message' => $message, 'is_message' => true, 'is_error' => false,
                        'text' => $message // for BC
                        );

    $flash_box->reset();

    return new lmbArrayDataset($result);
  }
}
?>
