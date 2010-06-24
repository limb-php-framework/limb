<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com 
 * @copyright  Copyright &copy; 2004-2009 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html 
 */

/**
 * @property Parity
 * @tag_class WactListItemTag
 * @package wact
 * @version $Id: parity.prop.php 7686 2009-03-04 19:57:12Z korchasa $
 */
class WactParityProperty extends WactCompilerProperty
{
  protected $temp_var;
  protected $has_increment = FALSE;

  function generateScopeEntry($code_writer)
  {
    $this->temp_var = $code_writer->getTempVariable();
    $code_writer->writePHP('$' . $this->temp_var . ' = 0;');
  }

  /**
   * @param WactCodeWriter
   */
  function generatePreStatement($code_writer)
  {
    if (!$this->has_increment)
    {
      $this->hasIncrement = TRUE;
      $code_writer->writePHP('$' . $this->temp_var . '++;');
    }
  }

  /**
   * @param WactCodeWriter
   */
  function generateExpression($code_writer)
  {
    $code_writer->writePHP('(( $' . $this->temp_var . ' % 2) ? "odd" : "even")');
  }

}

