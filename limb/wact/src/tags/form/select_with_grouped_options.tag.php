<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: select_with_grouped_options.tag.php 5933 2007-06-04 13:06:23Z pachanga $
 * @package    $package$
 */

require_once('limb/wact/src/tags/form/control.inc.php');
/**
* @tag select_with_grouped_options
* @known_parent WactFormTag
*/
class WactGroupedOptionsSelectTag extends WactControlTag
{
  protected $runtimeComponentName = 'WactGroupedOptionsSelectComponent';
  protected $runtimeIncludeFile = 'limb/wact/src/components/form/WactGroupedOptionsSelectComponent.class.php';

  function getRenderedTag()
  {
    return 'select';
  }

  function generateTagContent($code)
  {
    $code->writePHP($this->getComponentRefCode() . '->renderContents();');
  }
}
?>
