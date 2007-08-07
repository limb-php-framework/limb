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
 * class lmbFlashBoxErrorsFetcher.
 *
 * @package web_app
 * @version $Id: lmbFlashBoxErrorsFetcher.class.php 6221 2007-08-07 07:24:35Z pachanga $
 */
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

    return new lmbCollection($result);
  }
}

