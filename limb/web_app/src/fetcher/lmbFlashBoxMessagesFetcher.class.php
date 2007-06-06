<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */
lmb_require('limb/core/src/lmbCollection.class.php');
lmb_require('limb/web_app/src/fetcher/lmbFetcher.class.php');

/**
 * class lmbFlashBoxMessagesFetcher.
 *
 * @package web_app
 * @version $Id: lmbFlashBoxMessagesFetcher.class.php 5945 2007-06-06 08:31:43Z pachanga $
 */
class lmbFlashBoxMessagesFetcher extends lmbFetcher
{
  protected function _createDataSet()
  {
    $result = array();

    $flash_box = lmbToolkit :: instance()->getFlashBox();
    foreach($flash_box->getMessages() as $message)
      $result[] = array('message' => $message,
                        'text' => $message // for BC
                        );

    $flash_box->resetMessages();

    return new lmbCollection($result);
  }
}
?>
