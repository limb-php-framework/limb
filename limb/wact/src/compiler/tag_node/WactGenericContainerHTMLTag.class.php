<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * Compile time component for tags in template which are not
 * recognized WACT tags but have a runat="server" attribute.
 * This allows native HTML tags, for example, to be manipulated
 * at runtime.
 * WactGenericContainerHTMLTag is for tags with children.
 * @package wact
 * @version $Id: WactGenericContainerHTMLTag.class.php 6221 2007-08-07 07:24:35Z pachanga $
 */
class WactGenericContainerHTMLTag extends WactRuntimeComponentHTMLTag
{
  protected $runtimeIncludeFile;
  protected $runtimeComponentName = 'WactRuntimeTagComponent';

  function generateAfterOpenTag($code_writer)
  {
    $code_writer->writePHP($this->getComponentRefCode() . '->render();');
  }
}


