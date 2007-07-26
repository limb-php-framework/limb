<?php
class lmbZendSearchIndexer
{
  protected $normalizer = null;
  protected $left_bound = '<!-- no index start -->';
  protected $right_bound = '<!-- no index end -->';
  protected $use_noindex = false;
 
  protected $index;
 
  function __construct($normalizer = null)
  {
    $this->normalizer = $normalizer;
  }
 
  function useNOINDEX($status = true)
  {
    $this->use_noindex = $status;
  }
 
  function index($uri, $content)
  {
    $content = $this->_getIndexedContent($content);
    
    $doc = Zend_Search_Lucene_Document_Html::loadHTML(lmb_strtolower($content), true);
    $doc->getField('title')->boost = 1.5;
    $doc->addField(Zend_Search_Lucene_Field::UnIndexed('uri', $uri->toString()));
 
    $index = $this->_getIndex();

    @$index->addDocument($doc);
  }
 
  function _getIndex()
  {
    if(!$this->index)
      $this->index = Zend_Search_Lucene::create(LIMB_VAR_DIR . '/search_index');
    return $this->index;
  }
 
  function _getIndexedContent($content)
  {
    if(!$this->use_noindex)
      return $content;
 
    $regex = '~' .
             preg_quote($this->left_bound) .
             '(.*?)' .
             preg_quote($this->right_bound) .
             '~s';
 
    return preg_replace($regex, ' ', $content);
  }
}
?>