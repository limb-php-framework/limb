<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/tags/form/control.inc.php');
/**
 * @tag select_with_grouped_options
 * @known_parent WactFormTag
 * @package wact
 * @version $Id: select_with_grouped_options.tag.php 6243 2007-08-29 11:53:10Z pachanga $
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

