<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

/**
 * class lmbMacroPassiveTag.
 *
 * @package macro
 * @version $Id$
 */
class lmbMacroPassiveTag extends lmbMacroTag 
{

  function generate($code_writer)
  {
  }
  
  function generateNow($code_writer)
  {
    parent :: generate($code_writer);
  }
}

