<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * interface WactTemplateLocator.
 *
 * @package wact
 * @version $Id: WactTemplateLocator.interface.php 6243 2007-08-29 11:53:10Z pachanga $
 */
interface WactTemplateLocator
{
  public function locateCompiledTemplate($fileName);
  public function locateSourceTemplate($fileName);

  public function readTemplateFile($fileName);
}

