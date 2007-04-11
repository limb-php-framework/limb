<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFlashBoxErrorsFetcher.class.php 5629 2007-04-11 12:13:16Z pachanga $
 * @package    web_app
 */
lmb_require('limb/datasource/src/lmbIterator.class.php');
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

    return new lmbIterator($result);
  }
}
?>
