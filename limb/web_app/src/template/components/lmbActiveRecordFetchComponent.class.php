<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbFetchComponent.class.php 5012 2007-02-08 15:38:06Z pachanga $
 * @package    web_app
 */
lmb_require('limb/wact/src/components/fetch/WactFetchComponent.class.php');

class lmbActiveRecordFetchComponent extends WactFetchComponent
{
  protected $find_params = array();

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
?>