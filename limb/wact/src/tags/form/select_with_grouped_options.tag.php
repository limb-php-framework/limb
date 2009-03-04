<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

require_once('limb/wact/src/tags/form/control.inc.php');
/**
 * @tag select_with_grouped_options
 * @known_parent WactFormTag
 * @package wact
 * @version $Id: select_with_grouped_options.tag.php 7686 2009-03-04 19:57:12Z korchasa $
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

