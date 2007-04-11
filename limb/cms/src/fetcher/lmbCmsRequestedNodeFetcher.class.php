<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCmsRequestedNodeFetcher.class.php 5629 2007-04-11 12:13:16Z pachanga $
 * @package    cms
 */
lmb_require('limb/web_app/src/fetcher/lmbFetcher.class.php');
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');
lmb_require('limb/cms/src/model/lmbCmsNode.class.php');

class lmbCmsRequestedNodeFetcher extends lmbFetcher
{
  function _createDataSet()
  {
    $path = lmbToolkit :: instance()->getRequest()->getUriPath();
    return new lmbIterator(array(lmbCmsNode :: findByPath('lmbCmsNode', $path)));
  }
}
?>
