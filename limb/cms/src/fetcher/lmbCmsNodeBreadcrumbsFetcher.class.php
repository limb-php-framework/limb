<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

lmb_require('limb/web_app/src/fetcher/lmbFetcher.class.php');
lmb_require('limb/active_record/src/lmbActiveRecord.class.php');

class lmbCmsNodeBreadcrumbsFetcher extends lmbFetcher
{
  function _createDataSet()
  {
    $path = lmbToolkit :: instance()->getRequest()->getUri()->getPath();

    if(!$node = lmbCmsNode :: findByPath($path))
      return new lmbCollection();

    $parents = $node->getParents();

    $result = array();
    $skip_root = true;
    $path = '';

    foreach($parents as $parent)
    {
      if($skip_root)
      {
        $skip_root = false;
        continue;
      }
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
