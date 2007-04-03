<?php
/**
 * Limb Web Application Framework
 *
 * @link http://limb-project.com
 *
 * @copyright  Copyright &copy; 2004-2007 BIT
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 * @version    $Id: tree.tag.php 5021 2007-02-12 13:04:07Z pachanga $
 * @package    wact
 */

/**
 * Dumps the component tree into the compiled template
 * @tag dev:TREE
 */
class WactDevTreeTag extends WactCompilerTag
{
  /**
   * @param WactCodeWriter
   * @return void
   * @access protected
   */
  function preGenerate($code_writer) {
    parent::preGenerate($code_writer);
    $code_writer->writeHTML('<div aligh="left"><hr /><h3>Begin Tree Dump</h3><hr /></div>');
  }

  /**
   * @param WactCodeWriter
   * @return void
   * @access protected
   */
  function postGenerate($code_writer) {
    ob_start();
    dump_component_tree($this);
    $tree = ob_get_contents();
    ob_end_clean();
    $code_writer->writeHTML('<div align="left"><hr />'.$tree.
      '<hr /><br /><h3>End Tree Dump</h3><hr /></div>');
    parent::postGenerate($code_writer);
  }
}
?>