<?php
/**
 * class lmbMacroCopyTag.
 * @tag if
 * @req_attributes var
 * @restrict_self_nesting
 */
class lmbMacroIfTag extends lmbMacroTag
{
  protected function _generateBeforeContent($code_writer)
  {
    $code_writer->writePHP('if('.$this->get('var').') {');
  }

  protected function _generateAfterContent($code_writer)
  {
    $code_writer->writePHP('}');
  }
}
