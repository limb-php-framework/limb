<?php
/**
 * class lmbMacroCopyTag.
 * @tag elseif
 * @req_attributes var
 * @forbid_end_tag
 */
class lmbMacroElseIfTag extends lmbMacroTag
{
  protected function _generateContent($code_writer)
  {
    if(!$this->findParentByClass('lmbMacroIfTag'))
    {
      throw new lmbMacroException('If tag not found');
    }
    $code_writer->writePHP('} elseif('.$this->get('var').') {');
  }
}
