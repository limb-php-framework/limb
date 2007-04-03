<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: roweven.prop.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
 * @property ListRowEven
 * @tag_class WactListItemTag
 */
class WactListRowEvenProperty extends WactCompilerProperty
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
      $this->has_increment = TRUE;
      $code_writer->writePHP('$' . $this->temp_var . '++;');
    }
  }

  /**
   * @param WactCodeWriter
   */
  function generateExpression($code_writer)
  {
    $code_writer->writePHP('(( $' . $this->temp_var . ' % 2) == 0)');
  }
}
?>