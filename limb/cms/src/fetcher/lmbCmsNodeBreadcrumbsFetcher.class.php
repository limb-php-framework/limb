<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: lmbCmsNodeBreadcrumbsFetcher.class.php 5645 2007-04-12 07:13:10Z pachanga $
 * @package    cms
 */

lmb_require('limb/web_app/src/fetcher/lmbFetcher.class.php');
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');

class lmbCmsNodeBreadcrumbsFetcher extends lmbFetcher
{
  function _createDataSet()
  {
    $path = lmbToolkit :: instance()->getRequest()->getUri()->getPath();

    if(!$node = lmbCmsNode :: findByPath('lmbCmsNode', $path))
      return new lmbCollection();

    $parents = $node->getParents();

    $result = array();
    $path = '';

    foreach($parents as $parent)
    {
      $path .= '/' . $parent->get('identifier');
      $parent->setUrlPath($path);
      $result[] = $parent;
    }

    $node->setUrlPath($path . '/' . $node->getIdentifier());
    $node->setIsLast(true);
    $result[] = $node;

    return new lmbCollection($result);
  }

}

?>
