<?php
/**
 * class lmbMacroElseTag.
 * @tag else
 * @parent_tag_class lmbMacroIfTag
 * @forbid_end_tag
 */
class lmbMacroElseTag extends lmbMacroTag
{
  protected function _generateContent($code_writer)
  {
    $code_writer->writePHP('} else {'.PHP_EOL);
  }
}
