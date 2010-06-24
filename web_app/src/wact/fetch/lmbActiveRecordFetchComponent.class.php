<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
lmb_require('limb/wact/src/components/fetch/WactFetchComponent.class.php');

/**
 * class lmbActiveRecordFetchComponent.
 *
 * @package web_app
 * @version $Id$
 */
class lmbActiveRecordFetchComponent extends WactFetchComponent
{
  protected $find_params = array();

  function resetFindParams()
  {
    $this->find_params = array();
  }

  function addFindParam($value)
  {
    $this->find_params[] = $value;
  }

  protected function _createFetcher()
  {
    $fetcher = parent :: _createFetcher();
    $fetcher->setFindParams($this->find_params);
    return $fetcher;
  }
}

